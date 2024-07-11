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
        $tokenValid = $this->checkToken($_POST['token'] ?? "");
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValid) {
            $update = [
                'id' => $id,
                'title' => $_POST['title'],
            ];
            $itemManager->update($update);
            $this->addFlash('voila-success', 'item correctly updated');
            $this->redirectTo("/item");
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !$tokenValid) {
            $this->addFlash("voila-danger", "There is an attempt to post a form outside of the site's submission rules, or you session time is done");
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
        $tokenValid = $this->checkToken($_POST['token'] ?? "");

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValid) {
            $itemManager = new ItemManager();
            $item = [
                'title' => $_POST['title'],
            ];
            $id = $itemManager->insert($item);
            if ($id) {
                $this->addFlash('voila-success', 'item correctly created');
            } else {
                $this->addFlash('voila-danger', "there was a problem creating the item");
            }
            $this->redirectTo("/item");
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !$tokenValid) {
            $this->addFlash("voila-danger", "There is an attempt to post a form outside of the site's submission rules, or you session time is done");
        }

        return $this->twig->render('Item/add.html.twig');
    }


    /**
     * Handle item deletion
     *
     * @param int $id
     */
    public function delete()
    {
        $itemManager = new ItemManager();
        $tokenValid = $this->checkToken($_POST['token'] ?? "");

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValid) {
            $id = $_POST['item'];
            $itemManager->delete($id);
            $this->addFlash('voila-success', 'item correctly deleted');
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !$tokenValid) {
            $this->addFlash("voila-danger", "There is an attempt to post a form outside of the site's submission rules, or you session time is done");
        }

        $this->redirectTo('/item/index');
    }

    /**
     * Display item listing for API
     */
    public function itemAPIList()
    {
        $status = 500;
        $data = [];
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $itemManager = new ItemManager();
            $items = $itemManager->selectAll();
            if ($items) {
                $status = 200;
                $data["message"] = "Items found";
                $data["items"] = $items;
            } else {
                $status = 404;
                $data["message"] = "No items found";
            }
        } else {
            $status = 405;
            $data["message"] = "Method not allowed";
        }
        return $this->APIGetResponse($data, $status);
    }

    /**
     * Display one item for API
     */
    public function itemAPIShow()
    {
        $status = 500;
        $data = [];
        $item = $this->getItem();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($item) {
                $status = 200;
                $data["message"] = "Item found";
                $data["item"] = $item;
            } else {
                $status = 404;
                $data["message"] = "No item found";
            }
        } else {
            $status = 405;
            $data["message"] = "Method not allowed";
        }
        return $this->APIGetResponse($data, $status, 'POST');
    }

    /**
     * Display item Put for API
     */
    public function itemAPIEdit(): string
    {
        $status = 500;
        $data = [];
        $input = file_get_contents('php://input');
        $item = json_decode($input, true);
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            if ($item['id'] && $item['title']) {
                $update = [
                    'id' => $item['id'],
                    'title' => $item['title'],
                ];
                $itemManager = new ItemManager();
                $itemManager->update($update);
                $status = 200;
                $data["message"] = "Item updated";
                $data["item"] =  $update;
            } else {
                $status = 400;
                $data["message"] = "No item for this id or Missing id or title";
            }
        } else {
            $status = 405;
            $data["message"] = "Method not allowed";
        }

        return $this->APIGetResponse($data, $status, 'PUT');
    }

    /**
     * Display item creation for API
     */
    public function itemAPIadd()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemManager = new ItemManager();
            $input = file_get_contents('php://input');
            $inputData = json_decode($input, true);
            if ($inputData['title']) {
                $item = [
                    'title' => $inputData['title'],
                ];
                $id = $itemManager->insert($item);
                if ($id) {
                    $status = 201;
                    $data["message"] = "Item created";
                    $item = $itemManager->find($id);
                    $data["item"] = $item;
                } else {
                    $status = 503;
                    $data["message"] = "There was a problem creating the item";
                }
            } else {
                $status = 400;
                $data["message"] = "Missing title";
            }
        } else {
            $status = 405;
            $data["message"] = "Method not allowed";
        }

        return $this->APIGetResponse($data, $status, 'POST');
    }

    /**
     * Handle item deletion for API
     */
    public function itemAPIDelete()
    {

        $status = 500;
        $data = [];
        $item = $this->getItem();
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            if ($item) {
                $itemManager = new ItemManager();
                $itemManager->delete($item['id']);
                $status = 200;
                $data["message"] = "Item deleted";
            } else {
                $status = 400;
                $data["message"] = "No item for this id or Missing id";
            }
        } else {
            $status = 405;
            $data["message"] = "Method not allowed";
        }

        return $this->APIGetResponse($data, $status, 'DELETE');
    }

    private function getItem(): array
    {
        $input = file_get_contents('php://input');
        $inputData = json_decode($input, true);
        $id = $inputData["id"] ?? null;
        if ($id) {
            $itemManager = new ItemManager();
            $item = $itemManager->find($id);
        } else {
            $item = [];
        }
        return $item;
    }
}
