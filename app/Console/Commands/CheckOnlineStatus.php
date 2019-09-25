<?php
 
namespace App\Console\Commands;
 
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
 
class CheckOnlineStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'online:status';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check users online status';
 
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
 
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Carbon::now()->timestamp
        $users = DB::table('users')->get();

        foreach($users as $user)
        {
            if($user->exp < Carbon::now()->timestamp)
            {
                DB::table('users')->where('id', $user->id)->update(['status' => 0]);
            }
        }
    }
}