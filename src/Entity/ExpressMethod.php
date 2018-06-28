<?php

namespace Drupal\commerce_express\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Express method entity.
 *
 * @ConfigEntityType(
 *   id = "commerce_express_method",
 *   label = @Translation("快递方式"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\commerce_express\ExpressMethodListBuilder",
 *     "form" = {
 *       "add" = "Drupal\commerce_express\Form\ExpressMethodForm",
 *       "edit" = "Drupal\commerce_express\Form\ExpressMethodForm",
 *       "delete" = "Drupal\commerce_express\Form\ExpressMethodDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\commerce_express\ExpressMethodHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "commerce_express_method",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/commerce/config/express/commerce_express_method/{commerce_express_method}",
 *     "add-form" = "/admin/commerce/config/express/commerce_express_method/add",
 *     "edit-form" = "/admin/commerce/config/express/commerce_express_method/{commerce_express_method}/edit",
 *     "delete-form" = "/admin/commerce/config/express/commerce_express_method/{commerce_express_method}/delete",
 *     "collection" = "/admin/commerce/config/express/commerce_express_method"
 *   }
 * )
 */
class ExpressMethod extends ConfigEntityBase implements ExpressMethodInterface
{

    /**
     * The Express method ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The Express method label.
     *
     * @var string
     */
    protected $label;

    /**
     * 固定运费
     * @var float
     */
    protected $cost;

    /**
     * @return float
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param $amount float
     * @return $this
     */
    public function setCost($amount)
    {
        $this->cost = $amount;
        return $this;
    }
}
