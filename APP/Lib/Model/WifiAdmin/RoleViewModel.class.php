<?php
class RoleViewModel extends ViewModel{
    public $viewFields  = array(
        'role_user'=>array('role_id','user_id'),
        'admin'=>array('id','user','_on'=>'role_user.user_id = admin.id'),
        'role'=>array('name'=>'name','_on'=>'role_user.role_id = role_id'),
    );
}