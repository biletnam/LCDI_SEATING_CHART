<h1><?php echo __('New Task') ?></h1>


<?php 
  if($sf_request->hasParameter('projects_id'))
  {
    include_partial('form', array('form' => $form));
  }
  elseif(isset($choices['']) and count($choices)==1)
  {
    echo __('Cases Not Found');
  }  
  else
  {              
    echo __('Case') . ': ' . select_tag('form_projects_id','',array('choices'  => $choices),array('onChange'=>'load_form_by_projects_id(\'form_container\',\'' . url_for('tasks/newTask') . '\',this.value)','style'=>'width:450px;'));
?>

<div style="margin-top: 10px;" id="form_container"></div>

<script type="text/javascript">
  $(document).ready(function(){ 
      load_form_by_projects_id('form_container','<?php echo url_for("tasks/newTask") ?>',$('#form_projects_id').val());            
  });     
</script>

<?php } ?>
