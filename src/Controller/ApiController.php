<?php

namespace App\Controller;

use App\Model\ItemManager;
use App\Controller\ItemController;

/**
 * Class APIController
 *
 */
class APIController
{


  /**
   * Display item listing for API
   */
  public function itemList()
  {
    $ItemController = new ItemController();
    return $ItemController->itemAPIList();
  }


  /**
   * Display item informations specified by $id for API
   */
  public function itemShow()
  {
    $ItemController = new ItemController();
    return $ItemController->itemAPIShow();
  }

  /**
   * Display item edition specified by $id for API
   */
  public function itemEdit()
  {
    $ItemController = new ItemController();
    return $ItemController->itemAPIEdit();
  }

  /**
   * Display item creation for API
   */
  public function itemAdd()
  {
    $ItemController = new ItemController();
    return $ItemController->itemAPIAdd();
  }

  /**
   * Handle item deletion for API
   */
  public function itemDelete()
  {
    $ItemController = new ItemController();
    return $ItemController->itemAPIDelete();
  }
}
