
    <!-- Main Content -->
    <div class="container-fluid">
        <div class="side-body">
            <h4><?php echo isset($title) ? $title : "Portal"; ?> </h4>

            <div class="panel">
                <div class="panel-body">
                  <a class="btn btn-primary" id="btnaddresource" href="<?=site_url('file/add');?>">Add resource <i class="fa fa-upload"></i></a>
                   </div>

                <div class="panel-body" style="display: none;" id="div-add-resource">
                    <div class="result"></div>
                    <form class="form" method="post" action="file/save_resource" enctype="multipart/form-data" id="frmresources" name="frmresources">
                        <div class="form-group">
                            <label class="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Type title here">
                        </div>
                        <div class="form-group">
                            <label class="title">Description</label>
                            <textarea type="text" name="desc" id="desc" class="form-control" placeholder="Type short descrition here"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="title">Keyword</label>
                            <input type="text" name="tags" id="tags" class="form-control" placeholder="Type keyword here separate by comma eg. hello,hi,world">
                        </div>

                        <div class="form-group">
                            <label class="title">Upload</label>
                            <input type="file" name="filez" id="filez" class="btn alert-info" accept="image/*,audio/mp3,video/*,application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint,
text/plain, application/pdf,.docx,.pptx,.xlsx">
                        </div>
                        <div class="from-group">
                            <label></label><button class="btn btn-info" id="btnsave">Save</button>
                        </div>
                    </form>
                </div>
            <div class="panel-body">
                <form class="form" action="<?=site_url('file/index');?>" method="get">
                    <div class="form-inline">
                        <input type="text" name="q" id="q" class="form-control" style="width:95%;" placeholder="Search here.." value="<?=isset($_POST['q']) ? $_POST['q'] : '';?>"><button class="btn btn-default"><i class="fa fa-search"></i></button>
                    </div>
                </form>
            </div>
            </div>
            <div class="content-area">
                
    <?php 
    if (isset($list_life)) {
        # code...
        if (is_array($list_life)) {
            # code...
            echo "<table class='table table-striped'>";
            echo "<thead>
            <tr>
            <th>TITLE</th>
            <th>DESCRIPTION</th>
            <th class='hidden'>STUDENT</th>
            <th></th>
            </tr>
            </thead>";
            echo "<tbody>";
            foreach ($list_life as $key) {
                # code...
                # code...

     
                echo "<tr   id='td-$key->page_id'>
                    <td>$key->title</td>
                    <td>$key->description</td>
                    <td width='70px' id='td-$key->page_id' class='hidden'>YES</td>
                    <td width='110px'><a class='btn btn-default' href='file/edit?id=$key->page_id'><i class='fa fa-edit'></i></a> <span class='btn btn-danger'  onclick='delete_file(\"$key->page_id,1\")'><i class='fa fa-trash-o'></i></span></td>
                    
                </tr>"  ;

            }
            echo "</tbody>";
            echo "</table>";
        }
     } ?>
            </div>
           
         
        </div>
    </div>

</div>

<script type="text/javascript">
   

    function delete_file(id,status) {
        // body...
        //alert(status);return false;
            $.ajax({
                type: 'post',
                url: '<?=site_url("file/delete_file");?>',
                data: 'file_id='+id+'&status='+status,
                dataType:'json',
                success: function (resp) {
                    console.clear();
                    console.log(resp);
                    if (resp.stats == true) {
                        if (status == 1) {

                        $('#td-'+id+ ' span').remove(); 
                    }else{

                        $('#td-'+id+ ' span').remove();  
                    }                   
                    }

                }
            });
    }

</script>