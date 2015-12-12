<?php
namespace Example;

use Zend\Permissions\Acl\Acl as ZendAcl;

class Acl extends ZendAcl
{
    public function __construct()
    {
        // These are the roles in our application
        $this->addRole('guest');
        // member role "extends" guest, meaning the member role will get all of 
        // the guest role permissions by default
        $this->addRole('user', 'guest');
        $this->addRole('admin');
        $this->addRole('special', 'user');
        // $this->addRole('user');

        // These are the resources in our app. The resources are the 
        // applications's route patterns
        $this->addResource('/');
        $this->addResource('/login');
        $this->addResource('/logout');
        $this->addResource('/sales');
        $this->addResource('/products');
        $this->addResource('/categories');
        $this->addResource('/category');
        $this->addResource('/admin');

        // Now we allow or deny a role's access to resources. The third argument
        // is 'privilege'. We're using HTTP method for resources.
        // $this->allow('guest', '/', array('GET', 'POST'));
        $this->allow('guest', '/login', array('GET', 'POST'));
        $this->allow('guest', '/logout', 'GET');

        $this->allow('user', '/sales', 'GET');

        $this->allow('special', '/products', 'GET');

        // This allows admin access to everything
        $this->allow('admin');
    }
}