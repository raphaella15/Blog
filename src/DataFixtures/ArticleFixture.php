<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

//use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Article;

class ArticleFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //on donne des commentaires
            $article = new Article();
            for ( $k=1; $k <= mt_rand(4,10); $k++)
            {
              $comment = new Comment();

              $content = '<p>'."Lorem ipsum dolor sit, amet consectetur adipisicing elit. Excepturi dolore tempora odio autem incidunt minima recusandae nemo eaque quasi? Sequi quisquam pariatur quasi iste iusto, accusantium possimus veniam deserunt vitae maxime repellat alias natus eius quos nobis ipsum molestias quae!".'</p><p>'.'</p>';

              $days = (new \DateTime())->diff($article->getCreatedAt())->days;
              $comment ->setAuthor("john Steven")
                       ->setContent($content)
                       ->setCreatedAt(new \Datetime)
                       ->setArticle($article);

              $manager->persist($comment);
            }

      $manager->flush();
    }
}
