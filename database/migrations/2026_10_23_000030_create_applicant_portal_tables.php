<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 80);
            $table->string('last_name', 80);
            $table->string('email')->unique();
            $table->string('phone', 40)->nullable();
            $table->string('password');
            $table->string('resume_path')->nullable();
            $table->string('resume_name')->nullable();
            $table->timestamp('resume_updated_at')->nullable();
            $table->rememberToken();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        Schema::create('interview_slots', function (Blueprint $table) {
            $table->id();
            $table->dateTime('starts_at');
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            $table->string('location')->default('Head Office – 8 Desaine Street, St. Augustine');
            $table->unsignedTinyInteger('capacity')->default(1);
            $table->string('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('starts_at');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('applicant_id')->nullable()->after('job_posting_id')->constrained()->nullOnDelete();
            $table->foreignId('interview_slot_id')->nullable()->after('interview_at')->constrained()->nullOnDelete();
            $table->string('interview_location')->nullable()->after('interview_slot_id');
        });

        Schema::create('applicant_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20)->default('system');  // status | interview | message | system
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('link')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['applicant_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_notifications');
        Schema::table('applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('applicant_id');
            $table->dropConstrainedForeignId('interview_slot_id');
            $table->dropColumn('interview_location');
        });
        Schema::dropIfExists('interview_slots');
        Schema::dropIfExists('applicants');
    }
};
