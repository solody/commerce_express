<?php

namespace Drupal\commerce_express\Form;

use Drupal\commerce_express\Entity\ExpressMethodInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ExpressMethodForm.
 */
class ExpressMethodForm extends EntityForm
{

    /**
     * {@inheritdoc}
     */
    public function form(array $form, FormStateInterface $form_state)
    {
        $form = parent::form($form, $form_state);
        /** @var ExpressMethodInterface $commerce_express_method */
        $commerce_express_method = $this->entity;
        $form['label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('名称'),
            '#maxlength' => 255,
            '#default_value' => $commerce_express_method->label(),
            '#description' => $this->t("Label for the Express method."),
            '#required' => TRUE,
        ];

        $form['cost'] = [
            '#type' => 'commerce_number',
            '#title' => $this->t('运费'),
            '#default_value' => $commerce_express_method->getCost(),
            '#min' => 0,
            '#required' => TRUE,
        ];

        $form['id'] = [
            '#type' => 'machine_name',
            '#default_value' => $commerce_express_method->id(),
            '#machine_name' => [
                'exists' => '\Drupal\commerce_express\Entity\ExpressMethod::load',
            ],
            '#disabled' => !$commerce_express_method->isNew(),
        ];

        /* You will need additional form elements for your custom properties. */

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $form, FormStateInterface $form_state)
    {
        $commerce_express_method = $this->entity;
        $status = $commerce_express_method->save();

        switch ($status) {
            case SAVED_NEW:
                drupal_set_message($this->t('Created the %label Express method.', [
                    '%label' => $commerce_express_method->label(),
                ]));
                break;

            default:
                drupal_set_message($this->t('Saved the %label Express method.', [
                    '%label' => $commerce_express_method->label(),
                ]));
        }
        $form_state->setRedirectUrl($commerce_express_method->toUrl('collection'));
    }

}
