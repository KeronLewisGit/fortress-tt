<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('department', 80)->nullable();      // Operations, Cash in Transit, Electronics, Admin…
            $table->string('location')->default('Trinidad & Tobago');
            $table->string('employment_type', 30)->default('full_time'); // full_time | part_time | contract | shift
            $table->string('summary', 600);
            $table->text('description')->nullable();
            $table->json('responsibilities')->nullable();
            $table->json('requirements')->nullable();
            $table->json('benefits')->nullable();
            $table->string('salary_note')->nullable();          // free text e.g. "TT$ 25 – 30 / hr"
            $table->unsignedSmallInteger('openings')->default(1);
            $table->string('status', 20)->default('draft');    // draft | open | paused | closed
            $table->boolean('is_featured')->default(false);
            $table->boolean('requires_licence')->default(false);
            $table->boolean('requires_precept')->default(false);
            $table->date('closes_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
            $table->index(['status', 'published_at']);
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 12)->unique();
            $table->foreignId('job_posting_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name', 80);
            $table->string('last_name', 80);
            $table->string('email');
            $table->string('phone', 40);
            $table->string('address')->nullable();
            $table->string('area', 120)->nullable();             // town / region for deployment matching
            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 60)->default('Trinidad & Tobago');
            $table->string('licence_class', 10)->nullable();     // none | 3 | 4 | 5 | HT
            $table->boolean('has_precept')->default(false);
            $table->boolean('has_police_certificate')->default(false);
            $table->boolean('has_firearm_licence')->default(false);
            $table->unsignedTinyInteger('years_experience')->default(0);
            $table->string('education', 120)->nullable();
            $table->string('current_employer')->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('resume_path')->nullable();
            $table->string('resume_name')->nullable();
            $table->string('source', 40)->nullable();            // website | facebook | referral | walk_in | other
            $table->string('status', 20)->default('new');       // new | screening | interview | assessment | offer | hired | rejected | withdrawn
            $table->unsignedTinyInteger('rating')->nullable();   // 1-5
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('status_changed_at')->nullable();
            $table->timestamp('interview_at')->nullable();
            $table->boolean('consent')->default(true);
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->index(['email']);
        });

        Schema::create('application_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20)->default('note');        // note | status | email | interview | system
            $table->text('body');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->text('body');
            $table->string('set_status', 20)->nullable();       // optionally move applicant to this stage on send
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_templates');
        Schema::dropIfExists('application_notes');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('job_postings');
    }
};
