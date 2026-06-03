<?php

namespace Tests\Unit;

use App\Models\Fine;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsTest extends TestCase
{
    // RefreshDatabase wipes and re-creates the database before each test
    // so tests never interfere with each other
    use RefreshDatabase;

    // ══════════════════════════════════════════════════════════════════
    // TEST 1 — Fine calculation
    // ══════════════════════════════════════════════════════════════════
    /**
     * Test that Fine::calculateAmount() correctly computes the fine
     * amount based on the number of days overdue at $0.50 per day.
     *
     * UML reference: Fine.calculateAmount() operation
     */
    public function test_fine_calculate_amount(): void
    {
        // ARRANGE — set up test values
        $dailyRate = 0.50; // defined in Fine::DAILY_RATE

        // ACT — call the method we are testing
        $fineFor1Day  = Fine::calculateAmount(1);
        $fineFor7Days = Fine::calculateAmount(7);
        $fineFor14Days = Fine::calculateAmount(14);

        // ASSERT — check the results are what we expect
        // 1 day overdue = $0.50
        $this->assertEquals(0.50, $fineFor1Day,
            'Fine for 1 day overdue should be $0.50');

        // 7 days overdue = $3.50
        $this->assertEquals(3.50, $fineFor7Days,
            'Fine for 7 days overdue should be $3.50');

        // 14 days overdue = $7.00
        $this->assertEquals(7.00, $fineFor14Days,
            'Fine for 14 days overdue should be $7.00');

        // Zero days = zero fine
        $this->assertEquals(0.00, Fine::calculateAmount(0),
            'Fine for 0 days should be $0.00');
    }

    // ══════════════════════════════════════════════════════════════════
    // TEST 2 — Loan overdue detection
    // ══════════════════════════════════════════════════════════════════
    /**
     * Test that Loan::isOverdue() correctly identifies overdue loans.
     *
     * UML reference: Loan.isOverdue() operation
     */
    public function test_loan_is_overdue(): void
    {
        // ARRANGE — create a loan object (without saving to database)
        // We use 'make()' instead of 'create()' to avoid needing full DB setup

        // Scenario A: Loan is PAST due date and NOT returned = OVERDUE
        $overdueLoan = new Loan();
        $overdueLoan->dueDate    = Carbon::today()->subDays(5); // due 5 days ago
        $overdueLoan->returnDate = null;                        // not returned yet
        $overdueLoan->status     = 'ACTIVE';

        // Scenario B: Loan is NOT past due date = NOT overdue
        $activeLoan = new Loan();
        $activeLoan->dueDate    = Carbon::today()->addDays(9); // due in 9 days
        $activeLoan->returnDate = null;
        $activeLoan->status     = 'ACTIVE';

        // Scenario C: Loan is past due date BUT already returned = NOT overdue
        $returnedLoan = new Loan();
        $returnedLoan->dueDate    = Carbon::today()->subDays(3); // was due 3 days ago
        $returnedLoan->returnDate = Carbon::today()->subDays(1); // but was returned
        $returnedLoan->status     = 'RETURNED';

        // ACT & ASSERT
        $this->assertTrue(
            $overdueLoan->isOverdue(),
            'Loan past due date with no return date should be overdue'
        );

        $this->assertFalse(
            $activeLoan->isOverdue(),
            'Loan with future due date should NOT be overdue'
        );

        $this->assertFalse(
            $returnedLoan->isOverdue(),
            'Returned loan should NOT be overdue even if past due date'
        );

        // Also test getDaysOverdue()
        $this->assertEquals(5, $overdueLoan->getDaysOverdue(),
            'Loan due 5 days ago should report 5 days overdue'
        );

        $this->assertEquals(0, $activeLoan->getDaysOverdue(),
            'Active non-overdue loan should report 0 days overdue'
        );
    }

    // ══════════════════════════════════════════════════════════════════
    // TEST 3 — Member registration creates correct records
    // ══════════════════════════════════════════════════════════════════
    /**
     * Test that registering a new member creates both a User record
     * and a linked Member record with correct default values.
     *
     * UML reference: User → Member inheritance, Member attributes
     */
    public function test_member_registration_creates_user_and_member_records(): void
    {
        // ARRANGE — simulate what AuthController::register() does
        $userData = [
            'fullName'       => 'Test Member',
            'email'          => 'testmember@lms.test',
            'passwordHash'   => bcrypt('password123'),
            'role'           => 'MEMBER',
            'isActive'       => true,
        ];

        // ACT — create the User record
        $user = User::create($userData);

        // Create the linked Member record
        $member = Member::create([
            'userId'         => $user->userId,
            'memberId'       => 'MBR-' . str_pad($user->userId, 5, '0', STR_PAD_LEFT),
            'membershipType' => 'STANDARD',
            'joinDate'       => Carbon::today(),
            'expiryDate'     => Carbon::today()->addYear(),
            'totalFinesDue'  => 0,
        ]);

        // ASSERT — check the User was created correctly
        $this->assertDatabaseHas('users', [
            'email'    => 'testmember@lms.test',
            'fullName' => 'Test Member',
            'role'     => 'MEMBER',
            'isActive' => true,
        ]);

        // ASSERT — check the Member record was created and linked
        $this->assertDatabaseHas('members', [
            'userId'         => $user->userId,
            'membershipType' => 'STANDARD',
            'totalFinesDue'  => 0,
        ]);

        // ASSERT — check the memberId format is correct (MBR-00001 etc.)
        $this->assertStringStartsWith('MBR-', $member->memberId,
            'Member ID should start with MBR-'
        );

        // ASSERT — check membership expires exactly 1 year from today
        $this->assertEquals(
            Carbon::today()->addYear()->toDateString(),
            $member->expiryDate->toDateString(),
            'Membership should expire 1 year from registration date'
        );

        // ASSERT — check the relationship works both ways
        $this->assertEquals($user->userId, $member->user->userId,
            'Member should be linked back to the correct User'
        );

        // ASSERT — check role helpers work
        $this->assertTrue($user->isMember(),  'User role should be MEMBER');
        $this->assertFalse($user->isAdmin(),  'User should NOT be admin');
        $this->assertFalse($user->isLibrarian(), 'User should NOT be librarian');
    }
}