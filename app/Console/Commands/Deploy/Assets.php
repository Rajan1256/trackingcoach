<?php

namespace App\Console\Commands\Deploy;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Str;
use Symfony\Component\Process\Process;

class Assets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:assets {--branch=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy Assets if needed';

    /**
     * @var string[]
     */
    protected array $foldersToCheck = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->pathsToCheck = [
            'resources/js/',
            'resources/img/',
            'resources/css/',
            'tailwind.config.js',
        ];

        $this->pathsToCopy = [
            'public/css/',
            'public/js/',
            'public/img/',
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $storage = Storage::disk('local');
        $tmpFolder = 'tmp-git';

        // If parent run failed cleanup old dir
        if ($storage->exists($tmpFolder)) {
            $this->message("Delete old temporary checkout");
            $storage->deleteDirectory($tmpFolder);
        }

        $storage->makeDirectory($tmpFolder);

        $this->message("Checkout the repository");
        $process = Process::fromShellCommandline("git clone -b ".$this->option("branch")." git@github.com:creativeorange/trackingcoach.git ".$storage->path($tmpFolder));
        $process->run();

        if (!$process->isSuccessful()) {
            $this->message($process->getErrorOutput());
            exit(1);
        }

        $this->message("Finished checking out");


        $hashes = [];
        foreach ($this->pathsToCheck as $folder) {
            $process = Process::fromShellCommandline("cd ".$storage->path($tmpFolder)." && git log -1 --pretty=\"%H\" ".$folder);
            $process->run();

            if (!$process->isSuccessful()) {
                $this->message($process->getErrorOutput());
                exit(1);
            }

            $hashes ['hashes'][$folder] = (string) Str::of($process->getOutput())->trim();
        }

        $hashes ['hash'] = sha1(serialize($hashes['hashes']));

        // Get current file
        if ($storage->exists('assets.json')) {
            $content = $storage->get('assets.json');
            $hash = object_get(json_decode($content), 'hash');
        } else {
            $hash = null;
        }

        $storage->put('assets.json', json_encode($hashes));


        if ($storage->exists($tmpFolder)) {
            $this->message("Delete old temporary checkout");
            $storage->deleteDirectory($tmpFolder);
        }


        if ($hash !== $hashes['hash']) {
            $this->message("Hashes differ from each other, new assets!", true);

            $this->message("Installing NPM node_modules (grab a cup of coffee, will take some time)", true);
            $process = Process::fromShellCommandline("npm ci");
            $process->setTimeout(240);
            $process->run();

            if (!$process->isSuccessful()) {
                $this->message($process->getErrorOutput());
                exit(1);
            }

            $this->message($process->getOutput());

            $this->message("Compiling assets (still no coffee? there is some time left, will take a bit)", true);
            $process = Process::fromShellCommandline("npm run production");
            $process->setTimeout(240);
            $process->run();

            if (!$process->isSuccessful()) {
                $this->message($process->getErrorOutput());
                exit(1);
            }

            $this->message($process->getOutput());

            $this->message("Removing NPM folder (to late to grab a coffee, we can almost see the finish here)", true);
            $process = Process::fromShellCommandline("rm -rf node_modules/");
            $process->run();

            if (!$process->isSuccessful()) {
                $this->message($process->getErrorOutput());
                exit(1);
            }
        } else {
            $this->message("Assets are not changed!", true);

            foreach ($this->pathsToCopy as $folder) {
                $this->message("Checking and copying ".$folder);
                $process = Process::fromShellCommandline("cp -r ../../current/".$folder." public");
                $process->run();

                if (!$process->isSuccessful()) {
                    $this->message($process->getErrorOutput());
                    exit(1);
                }

                $this->message($process->getOutput());
            }

            $this->message("Copy manifest");
            $process = Process::fromShellCommandline("cp -r ../../current/public/mix-manifest.json public/mix-manifest.json");
            $process->run();

            if (!$process->isSuccessful()) {
                $this->message($process->getErrorOutput());
                exit(1);
            }

            $this->message($process->getOutput());
        }

        $this->message("Finished!", true);
    }

    /**
     * Print message
     *
     * @param  string  $message
     * @param  bool  $separator
     */
    protected function message(string $message, bool $separator = false)
    {
        $messageFormatted = '['.now()->toDateTimeString().'] '.$message;
        if ($separator) {
            $this->info(str_pad("", Str::length($messageFormatted), "="));
        }
        $this->info($messageFormatted);
    }
}
