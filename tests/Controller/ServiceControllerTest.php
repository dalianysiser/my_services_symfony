<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\SubCategory;
use App\Entity\Service;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ServiceControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/editor/service/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $userRepository = $this->manager->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('me@example.com');
        if($testUser){
            $testUser->setRoles(['ROLE_USER', 'ROLE_EDITOR','ROLE_ADMIN']);
            $this->manager->persist($testUser);
            $this->manager->flush();
        }
        $this->client->loginUser($testUser);

        
        $this->repository = $this->manager->getRepository(Service::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
        $this->createEntitys();
    }

    public function createEntitys():Subcategory{

        foreach ($this->manager->getRepository(SubCategory::class)->findAll() as $object) {
            $this->manager->remove($object);
        }
        $this->manager->flush();

        foreach ($this->manager->getRepository(Category::class)->findAll() as $object) {
            $this->manager->remove($object);
        }
        $this->manager->flush();
        $category = new Category();
        $category->setName('Category');

        $this->manager->persist($category);
        $this->manager->flush();

        $fixture = new SubCategory();
        $fixture->setName('Sub');
        $fixture->setCategory($category);

        $this->manager->persist($fixture);
        $this->manager->flush();
        return $fixture
        ;
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();

        $crawler = $this->client->request('GET', $this->path);

       self::assertResponseStatusCodeSame(200);
       self::assertPageTitleContains('Service index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        //$this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));
        self::assertResponseStatusCodeSame(200);
        $sub= $this->manager->getRepository(SubCategory::class)->findOneBy(['name'=>'Sub']);
        $this->client->submitForm('Save', [
            'service[name]' => 'Testing',
            'service[description]' => 'Testing',
            'service[price]' => 4,
            'service[stock]' => 2,
            'service[subCategories]' => ''.$sub->getId()
        ]);
       self::assertResponseRedirects('/editor/service');

       self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $sub= $this->manager->getRepository(SubCategory::class)->findOneBy(['name'=>'Sub']);
        $fixture = new Service();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPrice('5');
        $fixture->addSubCategory($sub);

        $this->manager->persist($fixture);
        $this->manager->flush();

        $data=$this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Service');
        
        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $sub= $this->manager->getRepository(SubCategory::class)->findOneBy(['name'=>'Sub']);
        $fixture = new Service();
        $fixture->setName('Value');
        $fixture->setDescription('Value');
        $fixture->setPrice('7');
        $fixture->addSubCategory($sub);

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'service[name]' => 'Something',
            'service[description]' => 'Something New',
            'service[price]' => 7,
            'service[subCategories]' => ''.$sub->getId(),
        ]);

        self::assertResponseRedirects('/editor/service');

        $fixture = $this->repository->findAll();

        self::assertSame('Something', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame(7.0, $fixture[0]->getPrice());
        self::assertSame('Sub', $fixture[0]->getSubCategories()[0]->getName());
    }

     public function testRemove(): void
     {
        $sub= $this->manager->getRepository(SubCategory::class)->findOneBy(['name'=>'Sub']);
        $fixture = new Service();
        $fixture->setName('Value');
        $fixture->setDescription('Value');
        $fixture->setPrice(6);
        $fixture->addSubCategory($sub);

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/editor/service');
        self::assertSame(0, $this->repository->count([]));
    }
}
