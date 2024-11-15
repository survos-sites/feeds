<?php

namespace App\Command;

use App\Entity\Domain;
use Doctrine\ORM\EntityManagerInterface;
use GrabySiteConfig\SiteConfig\Files;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Yaml\Yaml;
use Zenstruck\Console\Attribute\Option;
use Zenstruck\Console\InvokableServiceCommand;
use Zenstruck\Console\IO;
use Zenstruck\Console\RunsCommands;
use Zenstruck\Console\RunsProcesses;

#[AsCommand('app:load-data', 'load the config files into the database')]
final class AppLoadDataCommand extends InvokableServiceCommand
{
    use RunsCommands;
    use RunsProcesses;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    public function __invoke(
        IO $io,

        #[Option(description: 'limit the number of config files loaded')]
        int $limit = 50,
    ): int {

        $files = Files::getFiles();
        $progressBar = new ProgressBar($io, count($files));
        $progressBar->start();
        foreach ($files as $fileName => $realPath) {
            $rules = file_get_contents($realPath);
            $repeatableKeys = ['body','strip','strip_id_or_class','test_url','test_contains'];
            $singleKeys = ['prune','tidy'];
            $data = [
                'test_url' => [],
                'body' => [],
                'strip' => [],
                'strip_id_or_class' => []
            ];

            foreach (explode("\n", $rules) as $line) {
                $line = trim($line);
                if (str_starts_with($line, '#')) {
                    continue;
                }
                if ($line) {
                    if (!str_contains($line, ':')) {
                        continue;
                    }
                    assert(str_contains($line, ':'), $line . "\n" . $rules);
                    [$key, $value] = explode(':', $line, 2);
                    $value = trim($value);
                    if (in_array($key, $repeatableKeys)) {
                        $data[$key][] = $value;
                    } else {
//                        assert(empty($data[$key]), $key . " " . $rules);
                        $data[$key] = $value;
                    }
                }
            }
//            dd($data, $yaml);
            $domain = (new Domain())
                ->setName(basename($realPath))
                ->setTestUrls($data['test_url'])
                ->setRules($rules);
            $this->entityManager->persist($domain);
            $progressBar->advance();
            if ($limit && $progressBar->getProgress() >= $limit) {
                break;
            }
        }
        $progressBar->finish();
        $this->entityManager->flush();

        $io->success($this->getName() . ' success.');

        return self::SUCCESS;
    }
}
