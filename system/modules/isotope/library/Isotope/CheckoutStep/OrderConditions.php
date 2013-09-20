<?php

/**
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009-2012 Isotope eCommerce Workgroup
 *
 * @package    Isotope
 * @link       http://www.isotopeecommerce.com
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */


namespace Isotope\CheckoutStep;
use Isotope\Interfaces\IsotopeProductCollection;


abstract class OrderConditions extends CheckoutStep
{

    /**
     * Returns true if order conditions are defined
     * @return  bool
     */
    public function isAvailable()
    {
        return (boolean) $this->objModule->iso_order_conditions;
    }

    /**
     * Generate the checkout step
     * @return  string
     */
    public function generate()
    {
        $this->import('Isotope\Frontend', 'IsotopeFrontend');
        $objForm = $this->IsotopeFrontend->prepareForm($this->iso_order_conditions, $this->strFormId);

        // Form not found
        if ($objForm == null)
        {
            return '';
        }

        $this->blnError = $objForm->blnHasErrors;
        $this->Template->enctype = $objForm->enctype;

        $objTemplate = new \Isotope\Template('iso_checkout_order_conditions');
        $objTemplate->attributes    = $objForm->attributes;
        $objTemplate->tableless        = $objForm->arrData['tableless'];

        $parse = function ($a) {
            return $a->parse();
        };

        $objTemplate->hidden = implode('', array_map($parse, $objForm->arrHidden));
        $objTemplate->fields = implode('', array_map($parse, $objForm->arrFields));

        return $objTemplate->parse();
    }

    /**
     * Return review information for last page of checkout
     * @return  string
     */
    public function review()
    {
        if (!$this->hasError())
        {
            if (is_array($_SESSION['FORM_DATA']))
            {
                foreach( $_SESSION['FORM_DATA'] as $name => $value )
                {
                    $this->objModule->arrOrderData['form_' . $name] = $value;
                }
            }

            if (is_array($_SESSION['FILES']))
            {
                foreach( $_SESSION['FILES'] as $name => $file )
                {
                    $this->objModule->arrOrderData['form_' . $name] = \Environment::get('base') . str_replace(TL_ROOT . '/', '', dirname($file['tmp_name'])) . '/' . rawurlencode($file['name']);
                }
            }
        }

        return '';
    }

    /**
     * Return array of tokens for email templates
     * @param   IsotopeProductCollection
     * @param   \Module
     * @return  array
     */
    public function getEmailTokens(IsotopeProductCollection $objCollection, \Module $objModule)
    {
        // @todo return form field values
        /*
        foreach ($objForm->arrFormData as $name => $value)
        {
            $this->objModule->arrOrderData['form_' . $name] = $value;
        }

        foreach ($objForm->arrFiles as $name => $file)
        {
            $this->objModule->arrOrderData['form_' . $name] = \Environment::get('base') . str_replace(TL_ROOT . '/', '', dirname($file['tmp_name'])) . '/' . rawurlencode($file['name']);
        }
        */

        return array();
    }
}
