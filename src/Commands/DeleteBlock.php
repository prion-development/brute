<?php

namespace Brute\Commands;

use Illuminate\Console\Command;

class DeleteBlock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brute:block {key} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all block attempts for a specific key.';

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
        //
    }
}
