<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    design
 * @package     default_default
 * @copyright   Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
?>
<?php
/**
 * @methods
 *  getTitle() - string
 *  getSaveUrl() - string
 *  getSections() - array
 *  getForm() - html
 */
?>

<div class="entry-edit">
    <div class="entry-edit-head">
        <h4 class="icon-head head-edit-form fieldset-legend">Search Store Inventory</h4>
    </div>
    <div class="fieldset ">
        <div class="hor-scroll">
            <form id ="zipcodeForm" action="<?php echo $this->getUrl('adminhtml/index/savee/'); ?>" method="POST" enctype="multipart/form-data">
                <?php echo $this->getBlockHtml('formkey'); ?>
                <table cellspacing="0" class="form-list">
                    <tbody>
                        <tr>
                            <td class="label"><label for="search_key">Upload file (CSV format)<span class="required">*</span></label></td>
                            <td class="value">
                                <input id='search_key' name="file" type="file" class='required-entry'/>
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="label"></td>
                            <td class="value">
                                <input type="submit" name="submit" value="Upload"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>