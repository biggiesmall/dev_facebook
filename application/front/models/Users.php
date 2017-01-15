<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: abdel-latifmabrouck
 * Date: 15/01/2017
 * Time: 13:39
 */
class Users extends CI_Model
{
    public function insertUser($id, $data){
        $this->db->where('id_facebook',$id);
        $q = $this->db->get('users');
        var_dump($q->num_rows());
        if ( $q->num_rows() > 0 )
        {
            $data = array(
                'last_name' => $data['last_name'],
                'first_name' => $data['first_name'],
                'gender' => $data['gender'],
                'email' => $data['email']
            );
            $this->db->where('id_facebook', $id);
            $this->db->update('users', $data);
        }else{
            $this->db->set('id_facebook', $id);
            $this->db->insert('users',$data);
        }
    }
}