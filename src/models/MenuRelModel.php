<?php

namespace spark\models;

/**
* MenuRelModel
*
*/
class MenuRelModel extends Model
{
    protected static $table = 'menus_rel';

    protected $queryKey = 'item_id';

    protected $autoTimestamp = false;

    /**
     * Delete the menu item and every menu item under it
     *
     * @param  integer $id
     * @return boolean
     */
    public function deleteNested($id)
    {
        $childrens = $this->select(['item_id'])->where('parent_id', '=', $id)->execute()->fetchAll();

        foreach ($childrens as $item) {
            $this->deleteNested($item['item_id']);
        }

        // Finally delete the entry
        return $this->delete($id);
    }
}
