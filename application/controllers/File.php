<?php 

class File extends CI_Controller
{
	public $uid;
	public $username;
	
	function __construct()
	{
		parent::__construct();
		$this->init();
	}
	public function init($value='')
	{
		if (!$this->aauth->is_loggedin()){
        	//redirect();
        	$this->uid = 0;
        	$this->username = 'guest'.rand(10,100);
        }else{
		$this->uid = $this->session->userdata('id');
		$this->username = $this->session->userdata('username');

        }

		$this->uid = $this->session->userdata('id');
		$this->username = $this->session->userdata('username');
		$this->load->model('file_m');
		$this->load->model('group_m');
		$this->load->model('post_m');
        $this->load->library("slug");
	}
	public function index($value='')
	{
		# code...
		if (!$this->aauth->is_loggedin()){
        	redirect();
        }	
        if($this->session->userdata['permit'] == 'students'){
        	redirect();
        }
		$this->permission->access();

		$limit = 20;

		$list_life = $this->file_m->lisfile($limit);

		$data['list_life']= $list_life;
		$data['title']= "Resource Portal - file directory";
		$this->load->view('admin/default/header',$data);
		$this->load->view('admin/default/menu',$data);
		$this->load->view('files/index_new',$data);
		$this->load->view('admin/default/footer',$data);
	}
		public function add($value='')
	{
		# code...
		if (!$this->aauth->is_loggedin()){
        	redirect();
        }	
        if($this->session->userdata['permit'] == 'students'){
        	redirect();
        }
		$this->permission->access();

		$limit = 20;

		$list_life = $this->file_m->lisfile($limit);
		$data['listgroup'] = $this->group_m->group_type(3);
		$data['category'] = $this->group_m->group_type(2);

		$data['list_life']= $list_life;
		$data['title']= "Resource Portal - file directory";
		$this->load->view('admin/default/header',$data);
		$this->load->view('admin/default/menu',$data);
		$this->load->view('files/addnew',$data);
		$this->load->view('admin/default/footer',$data);
	}
	public function edit()
	{
		# code...

		if($this->input->get()){
			if($this->input->get('id')){
				$post_id = $this->input->get('id');
        		$data['infos'] = $this->file_m->get_file_info_id($post_id);
		       // $post_id = $this->post_m->get_id_by_slug($slug);
		        $data['files'] = $this->file_m->get_files($post_id);
		        if($tags=$this->file_m->get_tags($post_id)){
		        	foreach ($tags as $key) {
		        		# code...
		        		$keys[] = $key->keyword;
		        	}
		        	$data['tags'] = implode(',', $keys);
		        }


        		$data['files'] = $this->file_m->get_files($post_id);


			}
		}else{
			echo 'No input received.';
			exit();
		}

		$data['listgroup'] = $this->group_m->group_type(3);
		$data['category'] = $this->group_m->group_type(2);
		$data['title'] = 'Edit post';

		$this->load->view('admin/default/header',$data);
		$this->load->view('admin/default/menu',$data);
		$this->load->view('files/update/update',$data);
		$this->load->view('admin/default/footer',$data);

	}
	
	public function delete_file(){
	    if($this->input->post()){
	       $post_id =  $this->input->post('file_id');
	    if($this->file_m->delete_file($post_id)){
				echo json_encode(array('stats'=>true));
			}else{

				echo json_encode(array('stats'=>false));
			}
	    }
	    else{
				echo json_encode(array('stats'=>false));
	    }
	}
	public function allowuser($value='')
	{
		# code...
		if (!$this->aauth->is_loggedin()){
        	redirect();
        }	
		# code...
		if ($this->input->post()) {
			# code...

			$id = $this->input->post('uid');
			$stats = $this->input->post('status');

			if($this->file_m->allow_user($id,$stats)){
				echo json_encode(array('stats'=>true));
			}else{

				echo json_encode(array('stats'=>false));
			}
		}
	}
	public function download($filename='',$title='',$post_id = false)
	{
		# code...
		$this->load->helper('download');

		$filename = urldecode($this->uri->segment(3));
		//$title = urldecode($this->uri->segment(4));
		$post_id = $this->uri->segment(4);
		if($this->uid){

		$stored = $this->file_m->save_download(array('post_id'=>$post_id,'user_id'=>$this->uid));

	}else{
		$ip = $this->pagecounter->get_ip();
		$stored = $this->file_m->save_download(array('post_id'=>$post_id,'ip_address'=>$ip));

	}

		$file_path = $this->file_m->get_link($filename,$post_id,true);
        //echo $file_path;
		if ($file_path) {
			# code...
		force_download($file_path,NULL);
		}
		echo "<html><title>Resource portal</title></html>";
	}
public function update_abstract($value='')
	{
		if (!$this->aauth->is_loggedin()){
        	redirect();
        }
        if ($this->input->post()) {
        	$input = (object) $this->input->post();
        	$tags = explode('-', str_replace('-', ' ', $this->slug->create($input->title)).'-'.str_replace(',', '-', $input->tags));
			$tags = array_unique(array_filter($tags));


			$date = "$input->years/$input->months/$input->days ".date('h:m:s');;


			$title_exist = $this->file_m->title($input->title);
			$id_by_title = $this->post_m->get_id_by_title($input->title);

			if($title_exist > 0 && $input->edit_id != $id_by_title){
				echo json_encode(array('stats'=>false,'msg'=>'Title not available.'));
				exit();
			}

			$info = array(
					'title'=>$input->title,
					'date_presented'=>date('Y-m-d h:m:s'),
					'slug'=>$this->slug->create($input->title),
					'date_created'=>$date,
					'group_course'=>$input->group_course,
					'status'=>$input->group_privacy,
					'posted_by'=>$this->uid,
					'page_id'=>$input->edit_id
					);

			if($update = $this->file_m->update_resource_info($info,$input->contents)){


			$grouping = $this->post_m->update_in_table('post_by_course',
				array(array(
					'course_group_id'=>$input->group_course,
					'post_id'=>$input->edit_id
				)));


				if (is_array($tags)) {
					$this->post_m->remove_tags($input->edit_id);
					foreach ($tags as $key) {
						# code...
					$insert = $this->post_m->save_tag($key,$input->edit_id);
						}
					}else{

					$insert = $this->post_m->save_tag($tags,$input->edit_id);
					}



				echo json_encode(array('stats'=>true,'msg'=>"Post successfully updated.",'slug'=>$this->slug->create($input->title),'post_id'=>$input->edit_id));
			}else{
				echo json_encode(array('stats'=>false,'msg'=>$input->edit_id));
			}

        }
	}
	public function save_captions($value='')
	{
		if (!$this->aauth->is_loggedin()){
        	redirect();
        }
        if ($this->input->post()) {
        	$input = (object)$this->input->post();

        	if(isset($input->title)){
        		$this->file_m->update_file_title($input->id,$input->title);
        	}elseif(isset($input->caption)){

        		$this->file_m->update_file_caption($input->id,$input->caption);
        	}

        }
    }
    public function remove_file($value='')
	{
		if (!$this->aauth->is_loggedin()){
        	redirect();
        }
        if ($this->input->post()) {
        	$input = (object)$this->input->post();

        	if(isset($input->id)){
        		$this->file_m->remove_one_file($input->id);
        	}

        }
    }
    
	public function save_abstract($value='')
	{
		if (!$this->aauth->is_loggedin()){
        	redirect();
        }
        if ($this->input->post()) {
        	$input = (object) $this->input->post();
        	$tags = explode('-', str_replace('-', ' ', $this->slug->create($input->title)).'-'.str_replace(',', '-', $input->tags));
			$tags = array_unique(array_filter($tags));


			$date = "$input->years/$input->months/$input->days ".date('h:m:s');;


			$exist = $this->file_m->title($input->title);
			if($exist > 0){
				echo json_encode(array('stats'=>false,'msg'=>'Title already used.'));
				exit();
			}

			$info = array(
					'title'=>$input->title,
					'date_presented'=>date('Y-m-d h:m:s'),
					'slug'=>$this->slug->create($input->title),
					'date_created'=>$date,
					'group_course'=>$input->group_course,
					'status'=>$input->group_privacy,
					'posted_by'=>$this->uid
					);

			if($post_id = $this->file_m->save_resource_info($info,$input->contents)){


			$grouping = $this->post_m->insert_in_table('post_by_course',
				array(
					'course_group_id'=>$input->group_course,
					'post_id'=>$post_id
				));


				if (is_array($tags)) {
					foreach ($tags as $key) {
						# code...
					$insert = $this->post_m->save_tag($key,$post_id);
						}
					}else{

					$insert = $this->post_m->save_tag($tags,$post_id);
					}

			$admin_permission = $this->file_m->post_perm($post_id,5,$this->uid);
			$staff_permission = $this->file_m->post_perm($post_id,4,$this->uid);


				echo json_encode(array('stats'=>true,'msg'=>"Post successfully added.",'slug'=>$this->slug->create($input->title),'post_id'=>$post_id));
			}else{
				echo json_encode(array('stats'=>false,'msg'=>$post_id));
			}

        }
	}
	public function save_file()
	{
		# code...
		if($this->input->post()){

			$input = (object)$this->input->post();
			$file = $input->btnInput;
			$upload = false;

			$count = count($_FILES[$file]['name']);
			$title = $this->post_m->get_title_by_id($input->post_id);
			$j=0;
			for ($i = 0; $i < $count; $i++) {

			    if($upload[$j] = $this->upload($title,$file,$i)){
			    	$upload[$j]['post_id'] = $input->post_id;
			    	$j++;
			    }

			}
			if ($j == 0) {
				# code...
					echo json_encode(array('stats'=>false,'msg'=>'Upload unsuccessful! No file selected/Invalid file.'));
					exit();
			}
			if ($upload) {

				if($uploaded = $this->file_m->save_file_array(array_filter($upload))){
					//var_dump($upload);
					echo json_encode(array('stats'=>true,'msg'=>$j.' of '.$count.' file uploaded.'));
					exit();
				}else{
					echo json_encode(array('stats'=>false,'msg'=>$uploaded));
					exit();
				}

			}else{

					echo json_encode(array('stats'=>false,'msg'=>'Upload unsuccessful! No file selected/Invalid file.'));
			}

		}
	}
	

	public function upload($title=false,$file,$i)
	{
		if (!$this->aauth->is_loggedin()){
        	redirect();
        }	
		# code...
            	$tmp_file = $_FILES[$file]['tmp_name'][$i];
            	$mimetype = mime_content_type($tmp_file);
              	$date =  date('y-m-d-h-m-s');
              		switch ($mimetype) {
	 				case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
	 					# code...
	 					$type='word';
	 					break;
	 				case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
	 					# code...
	 					$type='spreadsheet';
	 					break;
	 				case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
	 					# code...
	 					$type='powerpoint';
	 					break;
	 				case 'application/pdf':
	 					# code...
	 					$type='pdf';
	 					break;	 			
	 				case 'image/png':
	 					# code...
	 					$type='image';
	 					break;
	 				case 'image/jpeg':
	 					# code...
	 					$type='image';
	 					break;
	 				case 'image/jpg':
	 					# code...
	 					$type='image';
	 					break;
	 				case 'image/gif':
	 					# code...
	 					$type='image';
	 					break;
	 				case 'image/tiff':
	 					# code...
	 					$type='photoshop';
	 					break;
	 				case 'image/vnd.adobe.photoshop':
	 					$type = 'photoshop';
	 					break;	
	 				case 'video/mp4':
	 					# code...
	 					$type='video';
	 					break;
	 				case 'application/octet-stream':
	 					# code...
	 					$type='video';
	 					break;
	 				case 'application/x-dosexec':
	 					# code...
	 					$type='exefile';
	 					break;

	 				case 'text/html':
	 					# code...
	 					$type='block';
	 					break;	
	 				case 'text/x-php':
	 					# code...
	 					$type='block';
	 					break;	 						
	 				case 'text/plain':
	 					# code...
	 					$type='block';
	 					break;	

	 				case 'application/x-rar':
	 					# code...
	 					$type='zipped';
	 					break;	 						
	 				case 'application/zip':
	 					# code...
	 					$type='zipped';
	 					break;	
	 				default:
	 					$type = 'others';
	 					break;
	 				}
	 				if($type === 'block' || $type === 'exefile'){
	 					return false;
	 				}
	 				if($type === 'word' || $type === 'powerpoint' || $type === 'spreadsheet'  || $type === 'pdf'){
	 					$ftype = 'docs';
	 				}else{
	 						$ftype = $type;
	 				}
	 				if($file != $ftype){
	 					//echo $ftype;
	 					return false;
	 				}


	 				

            $dirname = $this->username;

			$target_dir = UPLOADPATH . $dirname . "/";
			if (!file_exists($target_dir)) {
               	mkdir(UPLOADPATH.$dirname,0777);
			} 
            $dirname2 = $this->username.'/'.$type;
			$target_dir2 = UPLOADPATH . $dirname2 . "/";

			if (!file_exists($target_dir2)) {
               	mkdir(UPLOADPATH.$dirname2,0777);
			} 
                
                $filename = basename($_FILES[$file]["name"][$i]);
                $targetfile = $target_dir2 . basename($_FILES[$file]["name"][$i]);

                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $oldname = pathinfo($filename, PATHINFO_FILENAME);

                $oldname = $this->slug->create($oldname);
                if($title){

                $title = $this->slug->create($title);
                $newfile = $target_dir2.$title.'-'.$oldname.'-'.$date.'.'.$ext;
                $newfilename = $title.'-'.$oldname.'-'.$date.'.'.$ext;
	            }else{

	                $newfile = $target_dir2.$oldname.'-'.$date.'.'.$ext;
	                $newfilename = $oldname.'-'.$date.'.'.$ext;
	            }


        		if(move_uploaded_file($tmp_file, $newfile)){

                    $data = array('link'=>$newfile,'mtype'=>$mimetype,'newfilename'=>$newfilename,'type'=>$type);
                    
                    return $data;
                }else{

                    return false;
                }
            
	}
	    function check_mimetype($filename)
    {
		if (!$this->aauth->is_loggedin()){
        	redirect();
        }	
        $mimetype = false;
        if(function_exists('mime_content_type')) {
            $mimetype = mime_content_type($filename); 

            $allowed = array('image/jpeg','image/pjpeg','image/png','image/x-png','audio/mp3','video/*','application/msword', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint','application/pdf');
            if (in_array($mimetype, $allowed)) {

                return true;
            }else{
                return false;
            }
        }
       return true;
    }

	public function save_other_info()
	{
		if (!$this->aauth->is_loggedin()){
        	redirect();
        }	
		# code...
		$slug = $this->input->post('slug');
		$post_id = $this->post_m->get_id_by_slug($slug);

		$r_name = $this->input->post('researcher');
		$r_pos = $this->input->post('researcher-position');

		if (!empty($r_name[0]) ) {

			$i = 0;
			foreach ($r_name as $key) {
				$researcher[] = array('name' =>$key ,'position'=>$r_pos[$i],'post_id'=>$post_id );
				$i++;
			}
			$res = $this->post_m->insert_other_info('col_researcher',$researcher);
		}


		$p_name = $this->input->post('panel');
		$p_pos = $this->input->post('panel-position');

		if (!empty($p_name[0])) {

			$i = 0;
			foreach ($p_name as $key1) {
				$panel[] = array('name' =>$key1 ,'position'=>$p_pos[$i],'post_id'=>$post_id );
				$i++;
			}
			$res1 = $this->post_m->insert_other_info('col_panel',$panel);
		}
		
		$c_name = $this->input->post('committee');
		$c_pos = $this->input->post('committee-position');

		if (!empty($c_name[0])) {

			$i = 0;
			foreach ($c_name as $key2) {

				$committees[] = array('name' =>$key2 ,'position'=>$c_pos[$i],'post_id'=>$post_id );
				$i++;
			}
			$res2 = $this->post_m->insert_other_info('col_committee',$committees);
		}
		

		$rating = $this->input->post('rating');
			$rate[0] = array('rating'=>$rating,'post_id'=>$post_id);
			$res3 = $this->post_m->insert_other_info('col_ratings',$rate);


		return json_encode(array('stats'=>true));
		
		
	}
	public function search_names($string='')
	{
		if (!$this->aauth->is_loggedin()){
        	redirect();
        }	
		# code...
		$string = $this->input->post('name');

		$msgs = $this->post_m->get_name($string);
			# code...
			$i = 0;
		$msg = '';
			foreach ($msgs as $key) {
				# code...

				$msg .="<li id='$i' onclick='get_selected(\"$key->name\")'>$key->name</li>";
				$i++;
			}
		
		echo json_encode(array('stats'=>true,'msg'=>$msg));
	}


}