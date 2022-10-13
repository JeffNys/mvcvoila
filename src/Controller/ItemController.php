<?php

namespace App\Controller;

use App\Model\ItemManager;

/**
 * Class ItemController
 *
 */
class ItemController extends AbstractController
{


    /**
     * Display item listing
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $itemManager = new ItemManager();
        $items = $itemManager->selectAll();

        return $this->twig->render('Item/index.html.twig', ['items' => $items]);
    }


/**
     * Display item informations specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function show(int $id)
    {
        $itemManager = new ItemManager();
        $item = $itemManager->find($id);
        if (!$item) {
            $this->addFlash("voila-warning", "there is a problem with this item");
            $this->redirectTo("/item");
        }

        return $this->twig->render('Item/show.html.twig', ['item' => $item]);
    }


    /**
     * Display item edition page specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function edit(int $id): string
    {
        $itemManager = new ItemManager();
        $item = $itemManager->find($id);
        if (!$item) {
            $this->addFlash("voila-warning", "there is a problem with this item");
            $this->redirectTo("/item");
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $update = [
                'id' => $id,
                'title' => $_POST['title']
            ];
            $itemManager->update($update);
            $this->addFlash('voila-success', 'item correctly updated');
            $this->redirectTo("/item");
        }

        return $this->twig->render('Item/edit.html.twig', ['item' => $item]);
    }


    /**
     * Display item creation page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function add()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemManager = new ItemManager();
            $item = [
                'title' => $_POST['title'],
            ];
            $id = $itemManager->insert($item);
            $this->addFlash('voila-success', 'item correctly created');
            $this->redirectTo("/item");
        }

        return $this->twig->render('Item/add.html.twig');
    }


    /**
     * Handle item deletion
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $itemManager = new ItemManager();
        $itemManager->delete($id);
        $this->addFlash('voila-success', 'item correctly deleted');
        $this->redirectTo('/item/index');
    }}
