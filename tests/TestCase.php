<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    private $token;

    public function __construct(string $name)
    {
        parent::setUp();
        parent::__construct($name);
        $this->token = Admin::find(1)->createToken('admin', ['is_admin', 'admin_master'],now()->addWeek())->plainTextToken;
    }

    protected function getToken()
    {
        return $this->token;
    }

    public function __destruct()
    {
        Admin::find(1)->tokens()->delete();
    }
}
