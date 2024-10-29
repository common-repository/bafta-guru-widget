<?php
$categoriesList = get_bafta_categories();

if ($_POST['clearCache'])
{

    echo '<div class="updated"><p><strong>Cache Cleared, categories updated.</strong></p></div>  ';

} elseif ($_POST['bafta_promo_hidden'] == 'Y')
{

    foreach ($categoriesList as $categoryList)
    {
        $categoryListValue = get_bafta_categories_value($categoryList);
        $categoryList = $_POST['bafta_promo_' . $categoryListValue];
        update_option('bafta_promo_' . $categoryListValue, $categoryList);

    }

?>  

    <div class="updated">
        <p>
            <strong>
        
            <?php
                _e('Options saved.');
            ?>
            
            </strong>
        </p>
    </div>  

<?php
}
?> 

<div class="guru-admin-header">


	<style type="text/css" media="screen">

		.guru-admin-header .guru-admin-column {float:left; width:50%;}
		.guru-admin-header .column {float:left; padding:15px 0 0 55px;}
		.guru-admin-header .column img {margin-bottom:10px;}
		.guru-admin-header .column dt {display:block; width:85px; float:left;}
		.guru-admin-header .column dd {margin-bottom:0;}
		.guru-admin-body {clear:both; padding-top:10px;}
		#scrolling-settings label {width:160px; float:left;}
		#scrolling-settings {padding-top:20px;}

	</style>

    <div class="guru-admin-column">
        <h1>BAFTA Guru - WP Plugin</h1>
        <p>
            Select from the Craft categories below to customise the video, audio and article content that is displayed on your website, via the handy BAFTA Guru Widget that is included as part of this Plugin. To insert the widget please go to your widget manager.
        </p>
        <a href="http://guru.bafta.org" target="_blank">Find out more about BAFTA Guru</a>

    </div>

    <div class="column">
        <img src="<?php echo WP_PLUGIN_URL; ?>/bafta-guru-widget/skins/bafta/bafta-guru-settings-logo.jpg" alt="BAFTA Guru logo" width="350" height="95" />
        <dl>
            <dt>Author : </dt>
            <dd>BAFTA</dd>
            <dt>Version : </dt>
            <dd>1.0</dd>
            <dt>FAQs : </dt>
            <dd><a href="http://guru.bafta.org/guru-wordpress-plugin-and-widget" target="_blank" title="Read the BAFTA Guru Widget FAQs">BAFTA Guru Widget FAQs</a></dd>
        </dl>
    </div>

</div>

<div class="guru-admin-body">

    <p>
		<strong>Craft categories</strong><br />
        Select categories to customise the content displayed in the BAFTA Guru Widget on your website:
    </p>  

    <div class="wrap">  
    
        <form name="bafta_promo_form" method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']);
?>">
            <input type="hidden" name="bafta_promo_hidden" value="Y"/>  
            <?php
                foreach ($categoriesList as $categoryList)
                {
                
                    $categoryListValue = get_bafta_categories_value($categoryList);
            ?>
            <p><input type="checkbox" name="bafta_promo_<?php echo $categoryListValue; ?>" value="1" <?php if (get_option('bafta_promo_' . $categoryListValue)){echo 'checked="checked"';} ?> /> <?php echo $categoryList; ?></p> 
            <?php
                }
            ?>

    		<p class="submit">  
                <input type="submit" name="Submit" value="<?php _e('Update Options', 'bafta_promo_trdom')
?>" />  
            </p>  
            <p class="submit">  
                <input type="submit" name="clearCache" value="<?php
_e('Clear Cache', 'bafta_promo_clrch')
?>" />  
                This will force an update of categories above. They will also be automatically updated each time you come to this screen.
            </p>  

        </form>  
    </div>  
</div>