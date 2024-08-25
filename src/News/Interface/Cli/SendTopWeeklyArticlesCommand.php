<?php

namespace App\News\Interface\Cli;

use App\News\Application\Query\GetWeeklyTopArticlesQuery;
use App\News\Application\Query\Handler\GetWeeklyTopArticlesHandler;
use App\News\Domain\Entity\Article;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendTopWeeklyArticlesCommand extends Command
{
    protected static $defaultName = 'app:send-top-weekly-articles';

    public static function getDefaultName(): ?string {
        return self::$defaultName;
    }

    private GetWeeklyTopArticlesHandler $getWeeklyTopArticlesHandler;
    private MailerInterface $mailer;
    private string $recipientEmail;

    public function __construct(
        GetWeeklyTopArticlesHandler $getWeeklyTopArticlesHandler,
        MailerInterface $mailer,
        string $recipientEmail
    ) {
        parent::__construct();
        $this->getWeeklyTopArticlesHandler = $getWeeklyTopArticlesHandler;
        $this->mailer = $mailer;
        $this->recipientEmail = $recipientEmail;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Send top weekly articles to email.')
            ->setHelp('This command sends the top weekly articles to a specified email address.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $query = new GetWeeklyTopArticlesQuery(10);
        $articles = ($this->getWeeklyTopArticlesHandler)($query);

        $emailContent = $this->generateEmailContent($articles);

        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($this->recipientEmail)
            ->subject('Top Weekly Articles')
            ->html($emailContent);

        $this->mailer->send($email);

        $output->writeln('Top weekly articles have been sent.');

        return Command::SUCCESS;
    }

    /**
     * @param Article[] $articles
     * @return string
     */
    private function generateEmailContent(array $articles): string
    {
        $content = '<h1>Top Weekly Articles</h1>';
        foreach ($articles as $article) {
            $content .= sprintf(
                '<h2>%s</h2><p>%s</p><p></p>',
                $article->getTitle(),
                $article->getShortDescription()
            );
        }

        return $content;
    }
}
