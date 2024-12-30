<?php

namespace App\Tests\Controller;

use App\Entity\SubCategory;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SubCategoryControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/sub/category/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(SubCategory::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();

        foreach ( $this->manager->getRepository(Category::class)->findAll() as $object) {
            $this->manager->remove($object);
        }
        $this->manager->flush();
        $this->createEntitys();
        
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('SubCategory index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);
        
        $id= $this->manager->getRepository(Category::class)->findOneBy(['name'=>'Category P']);
        $this->client->submitForm('Save', [
            'sub_category[name]' => 'Testing',
            'sub_category[category]' =>''.$id->getId()
        ]);

        self::assertResponseRedirects('/sub/category');

        self::assertSame(1, $this->repository->count([]));
    }

    public function createEntitys():Category{
       
        $category = new Category();
        $category->setId(1);
        $category->setName('Category P');

        $this->manager->persist($category);
        $this->manager->flush();
        return $category
        ;
    }

    public function testShow(): void
    {
        $id= $this->manager->getRepository(Category::class)->findOneBy(['name'=>'Category P']);
        $fixture = new SubCategory();
        $fixture->setName('My Title');
        $fixture->setCategory($id);

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('SubCategory');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $id= $this->manager->getRepository(Category::class)->findOneBy(['name'=>'Category P']);
        $fixture = new SubCategory();
        $fixture->setName('Value');
        $fixture->setCategory($id);

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'sub_category[name]' => 'Something New',
            'sub_category[category]' => ''.$id->getId(),
        ]);

        self::assertResponseRedirects('/sub/category');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame($id->getName(), $fixture[0]->getCategory()->getName());
    }

    public function testRemove(): void
    {   
        $id= $this->manager->getRepository(Category::class)->findOneBy(['name'=>'Category P']);
        $fixture = new SubCategory();
        
        $fixture = new SubCategory();
        $fixture->setName('Value');
        $fixture->setCategory($id);

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/sub/category');
        self::assertSame(0, $this->repository->count([]));
    }
}
