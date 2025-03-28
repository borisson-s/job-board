<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('jobs_offered', function (Blueprint $table) {
            $table->id();

            // ✅ employer_id (User model)
            $table->foreignIdFor(User::class, 'employer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // ✅ category_id
            $table->foreignIdFor(Category::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('description');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs_offered');
    }
};
