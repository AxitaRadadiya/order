<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) add customer-like columns to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'payment_terms')) {
                $table->string('payment_terms')->nullable()->after('website');
            }
            if (!Schema::hasColumn('users', 'gst_number')) {
                $table->string('gst_number')->nullable()->after('payment_terms');
            }
            if (!Schema::hasColumn('users', 'discount')) {
                $table->decimal('discount', 5, 2)->default(0)->after('gst_number');
            }
            if (!Schema::hasColumn('users', 'gst_treatment')) {
                $table->string('gst_treatment')->nullable()->after('discount');
            }
            if (!Schema::hasColumn('users', 'place_of_supply')) {
                $table->string('place_of_supply')->nullable()->after('gst_treatment');
            }
            if (!Schema::hasColumn('users', 'pan_number')) {
                $table->string('pan_number')->nullable()->after('place_of_supply');
            }
            if (!Schema::hasColumn('users', 'credit_limit')) {
                $table->decimal('credit_limit', 10, 2)->nullable()->after('pan_number');
            }
        });

        // 2) update order_masters.customer_id -> user_id and reference users
        if (Schema::hasTable('order_masters')) {
            // drop foreign key and add user_id column
            Schema::table('order_masters', function (Blueprint $table) {
                if (Schema::hasColumn('order_masters', 'customer_id')) {
                    try {
                        $table->dropConstrainedForeignId('customer_id');
                    } catch (\Throwable $e) {
                        if (Schema::hasColumn('order_masters', 'customer_id')) {
                            $table->dropForeign(['customer_id']);
                        }
                    }

                    if (!Schema::hasColumn('order_masters', 'user_id')) {
                        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                    }
                }
            });

            // copy values from customer_id -> user_id
            try {
                DB::table('order_masters')->whereNotNull('customer_id')->update(['user_id' => DB::raw('customer_id')]);
            } catch (\Throwable $e) {
                // ignore if copy fails
            }

            // drop old customer_id column
            Schema::table('order_masters', function (Blueprint $table) {
                if (Schema::hasColumn('order_masters', 'customer_id')) {
                    $table->dropColumn('customer_id');
                }
            });
        }

        // 3) update addresses.customer_id -> user_id
        if (Schema::hasTable('addresses')) {
            Schema::table('addresses', function (Blueprint $table) {
                if (Schema::hasColumn('addresses', 'customer_id')) {
                    try {
                        $table->dropConstrainedForeignId('customer_id');
                    } catch (\Throwable $e) {
                        if (Schema::hasColumn('addresses', 'customer_id')) {
                            $table->dropForeign(['customer_id']);
                        }
                    }

                    if (!Schema::hasColumn('addresses', 'user_id')) {
                        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                    }
                }
            });

            try {
                DB::table('addresses')->whereNotNull('customer_id')->update(['user_id' => DB::raw('customer_id')]);
            } catch (\Throwable $e) {
            }

            Schema::table('addresses', function (Blueprint $table) {
                if (Schema::hasColumn('addresses', 'customer_id')) {
                    $table->dropColumn('customer_id');
                }
            });
        }

        // 4) update bank_details.customer_id -> user_id
        if (Schema::hasTable('bank_details')) {
            Schema::table('bank_details', function (Blueprint $table) {
                if (Schema::hasColumn('bank_details', 'customer_id')) {
                    try {
                        $table->dropConstrainedForeignId('customer_id');
                    } catch (\Throwable $e) {
                        if (Schema::hasColumn('bank_details', 'customer_id')) {
                            $table->dropForeign(['customer_id']);
                        }
                    }

                    if (!Schema::hasColumn('bank_details', 'user_id')) {
                        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                    }
                }
            });

            try {
                DB::table('bank_details')->whereNotNull('customer_id')->update(['user_id' => DB::raw('customer_id')]);
            } catch (\Throwable $e) {
            }

            Schema::table('bank_details', function (Blueprint $table) {
                if (Schema::hasColumn('bank_details', 'customer_id')) {
                    $table->dropColumn('customer_id');
                }
            });
        }

        // 5) drop customers table
        if (Schema::hasTable('customers')) {
            Schema::dropIfExists('customers');
        }
    }

    public function down(): void
    {
        // recreate customers table (best-effort)
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('company_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('website')->nullable();
                $table->string('password')->nullable();
                $table->string('payment_terms')->nullable();
                $table->string('gst_number')->nullable();
                $table->decimal('discount', 5, 2)->default(0);
                $table->string('gst_treatment')->nullable();
                $table->string('place_of_supply')->nullable();
                $table->string('pan_number')->nullable();
                $table->decimal('credit_limit', 10, 2)->nullable();
                $table->timestamps();
            });
        }

        // revert order_masters.customer_id to reference customers
        if (Schema::hasTable('order_masters')) {
            Schema::table('order_masters', function (Blueprint $table) {
                if (Schema::hasColumn('order_masters', 'customer_id')) {
                    try {
                        $table->dropConstrainedForeignId('customer_id');
                    } catch (\Throwable $e) {
                        if (Schema::hasColumn('order_masters', 'customer_id')) {
                            $table->dropForeign(['customer_id']);
                        }
                    }

                    $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                }
            });
        }

        // remove added user columns
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'company_name', 'phone', 'website', 'payment_terms', 'gst_number', 'discount', 'gst_treatment', 'place_of_supply', 'pan_number', 'credit_limit'
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
