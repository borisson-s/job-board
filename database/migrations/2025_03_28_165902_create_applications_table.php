<?php

use App\ApplicationStatus;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Job::class)
                ->constrained('jobs_offered')
                ->cascadeOnDelete();

            $table->foreignIdFor(User::class, 'jobseeker_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('status')->default(ApplicationStatus::Pending->value);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
