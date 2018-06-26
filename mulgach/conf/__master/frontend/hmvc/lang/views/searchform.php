<?php 
$form = new mulForm(as_url('lang/search'),$this
		,array('mode'=>'get')
		);
$form->field($dr,'config')->hidden(['value'=>$config]);
$form->field($dr,'ep')->hidden(['value'=>$ep]);
?>
<table>
<tr>
<?php 
$langs = Lang::get_langs($config,$ep);
if(count($langs)>1)
{
	?>
	<td><?php $form->field($dr, 'lang')->ComboBox($langs); ?></td>
	<?php 
}
else 
	{
		?>
		<?php $form->field($dr, 'lang')->hidden(['value'=>$langs[0]]); ?>
		<?php 
	}	
?>
<td><?php $form->field($dr, 'langkey')->text(['htmlattrs'=>['placeholder'=>Lang::__t('language'),],
		'value'=>$request->_args['langkey'],
]);?></td>
<td><?php $form->field($dr, 'translation')->text(['htmlattrs'=>['placeholder'=>Lang::__t('translation')],
		'value'=>$request->_args['translation'],
]);?></td>
<td><?php $form->submit(Lang::__t('Search')) ?></td>
</tr>
</table>
<?php 
$form->close();
?>
