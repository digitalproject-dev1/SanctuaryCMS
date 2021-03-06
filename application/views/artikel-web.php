<div class="row">
    <div class="col-md-9">
      
      <?php if($artikel->status == true){ 

        foreach ($artikel->data as $key => $value) {
          # code...
        
        ?>

        <div class="media">
                <a href="//<?=base_url("artikel/".$value['id']."/".preg_replace("/( )/", "-", $value["judul"]))?>.html" class="text-dark"><img src="<?=$value["thumbnail"]?>" class="mr-3" alt="<?=$value["judul"]?>" style="width:150px;height:150px;"></a>
                <div class="media-body">
                  <h1 class="mt-0"><a href="//<?=base_url("artikel/".$value['id']."/".preg_replace("/( )/", "-", $value["judul"]))?>.html" class="text-dark"><?=$value["judul"]?></a></h1>
                  <hr>
                  <p>
                    <div><b>Kategori :</b> <a href="//<?=base_url("kategori/".$value['kategori']."/".preg_replace("/( )/", "-", $kategori->data[(int)$value["kategori"]]))?>"><span class="badge badge-success"><?=$kategori->data[(int)$value["kategori"]] ?></span></a>

                      </div>
                      <div><b>Tags :</b> 
                      <?php 
                        $tag_a = json_decode($value["tag"]);
                        for ($i=0; $i < count($tag_a); $i++) { 
                        ?>
                          <a href="//<?= base_url("tags/".(int)$tag_a[$i]."/".preg_replace("/( )/", "-", $tag->data[(int)$tag_a[$i]]))?>.html"><span class="badge badge-warning"><?=$tag->data[(int)$tag_a[$i]]; ?></span></a>
                      <?php
                        }
                      ?>
                      </div>
                  </p>
                
                <div>
                <small><i class="fa fa-calendar"></i> <?=$value["tgl_kirim"]?></small> <small><i class="fa fa-pencil"></i> <?=$users->data[$value["pengirim"]]?></small>
                </div>
                </div>
              </div>
        <div>
          <div id="display">
            <?=$value["isi"]?>
          </div>
          <script type="text/javascript">
            var editor = null;
              ClassicEditor
                        .create( document.getElementById('display'), {
                          /*ckfinder: {
                            uploadUrl: '//<?php echo base_url("perintah/ckeditor_image_receiver") ?>?command=QuickUpload&type=Files&responseType=json'
                          },*/
                          removePlugins : ["toolbar"]
                        } ).then(newEditor => {
                          editor = newEditor;
                          editor.isReadOnly = true;
                          const toolbarContainer = editor.ui.view.stickyPanel;
                          editor.ui.view.top.remove( toolbarContainer );

                          var main = document.querySelector("div.ck-editor__editable_inline");
                          main.setAttribute("style", "border:0px; background-color : #f8f9fa !important;");

                        }).catch( error => {
                            console.error( error );
                        } );

            
          </script>
          <hr>
          <div class="row" align="center">
            <div class="col">
              <a class="btn btn-block btn-outline-success" id="btn-like" onclick="like_dislike('like', <?=$id_artikel?>)">  
                <span class="badge badge-success" id="jml-like"><?=$value["jml_like"]?></span>  Suka
              </a>
            </div>
            <div class="col">
              <a class="btn btn-block btn-outline-danger" id="btn-dislike" onclick="like_dislike('like', <?=$id_artikel?>)">
                <span class="badge badge-danger" id="jml-dislike"><?=$value["jml_dislike"]?></span>  Tidak Suka
              </a>
            </div>
          </div>
          <hr>
        </div>

    <?php 
            }
        } else { ?>

      <div class="alert alert-warning" align="center"><?=$artikel->message?></div>

    <?php } ?>

      
      <form id="komentar">
        <div class="form-group">
          <label>Sebagai : </label>
          <input type="email" name="inisial" id="inisial" placeholder="contoh@mail.com" class="form-control" required maxlength="50" <?php if($this->session->has_userdata("email")){echo 'value="'.$this->session->userdata("email").'" readonly';} ?>>
        </div>
        <div class="form-group">
          <label>Berikan Komentar : </label>
          <textarea name="komentar" class="form-control" maxlength="255" required></textarea>
        </div>
        <div class="form-group">
          <label>Kode Verifikasi : <span id="interface-kode" class="badge badge-primary"><?=$verifikasi?></span></label>
          <input type="number" name="kode_verifikasi" class="form-control" placeholder="Ketik angka diatas!" required style="width: 20%">
        </div>
        <button type="submit" class="btn btn-success">Posting Komentar</button>
        <div align="center" style="margin-top: 10px;" id="alert-komentar"></div>
      </form>

      <script type="text/javascript">
        var check = <?php if($this->session->has_userdata("email")){ echo "true"; } else { echo "false"; } ?>;
        $("#komentar").submit(function(e){
          var notifi = document.getElementById("alert-komentar");
          e.preventDefault();
          var loader = document.createElement("i");
          loader.setAttribute("class", "fa fa-spinner fa-pulse");
          $.ajax({
            url: "//<?php echo base_url("kirim/komentar/".$id_artikel) ?>",
            type: "POST",
            data: $(this).serializeArray(),
            beforeSend: function(){
              notifi.removeAttribute("class");
              notifi.innerHTML = null;
              notifi.appendChild(loader);
            },
            success: function(output){
              notifi.innerHTML = null;
              if(output.status === true){
                notifi.setAttribute("class", "alert alert-success");
                check = true;
              } else {
                notifi.setAttribute("class", "alert alert-danger");
              }
              notifi.textContent = output.message;

              refresh_komentar(output);
            }
          });
        });



        function like_dislike(fungsi, id){
          var modal = document.getElementById("prop_modal");
          var modal_header = document.getElementById("modal-header");
          var modal_body = document.getElementById("modal-body");
          var modal_footer = document.getElementById("modal-footer");
          var btn_like = document.getElementById("btn-like");
          var jml_like = document.getElementById("jml-like");
          var btn_dislike = document.getElementById("btn-dislike");
          var jml_dislike = document.getElementById("jml-dislike");

          $(modal).on('hidden.bs.modal', function (e) {
            modal_header.innerHTML = null;
            modal_body.innerHTML = null;
            modal_footer.innerHTML= null;
          });


          

          if(check === true){
            
            $.ajax({
              url: "//<?=base_url("like/".$id_artikel) ?>",
              type: "POST",
              beforeSend: function(){
                $(btn_like).addClass("disabled");
                $(btn_dislike).addClass("disabled");
              },
              success: function(output){

              }
            });
          } else {
            $(modal).modal("show");
            modal_header.textContent = "Perhatian!";

            var p = document.createElement("p");
            p.textContent = "Sebelum itu mohon untuk memasukan email terlebih dahulu ya :), guna pendataan terhadap like dan dislike untuk artikel ini, dan setelah mengisi form ini anda tidak perlu melakukan pengisian form diartikel lain, terimakasih ^^";
            modal_body.appendChild(p);
            var hr = document.createElement("hr");
            modal_body.appendChild(hr);


            var form = document.createElement("form");

            modal_body.appendChild(form);

            var group1 = document.createElement("div");
            group1.setAttribute("class", "form-group");
            form.appendChild(group1);

            var label = document.createElement("label");
            label.textContent = "Email : ";
            group1.appendChild(label);

            var input = document.createElement("input");
            input.setAttribute("class", "form-control");
            input.setAttribute("name", "inisial");
            input.setAttribute("type", "email");
            input.setAttribute("maxlength", "50");
            group1.appendChild(input);

            var btn_submit = document.createElement("button");
            btn_submit.setAttribute("class", "btn btn-success");
            btn_submit.textContent = "Kirim";
            form.appendChild(btn_submit);

            var notifi = document.createElement("div");
            notifi.setAttribute("align", "center");
            notifi.setAttribute("style", "margin-top:10px;");

            var loader = document.createElement("i");
            loader.setAttribute("class", "fa fa-spinner fa-pulse");
            modal_body.appendChild(notifi);

            var form_inisial = document.getElementById("inisial");



            form.addEventListener("submit", function(e){
              e.preventDefault();
              $.ajax({
                url: "//<?=base_url("submit_email") ?>",
                type: "POST",
                data: $(form).serializeArray(),
                beforeSend: function(){
                  input.setAttribute("disabled", true);
                  btn_submit.setAttribute("disabled", true);
                  notifi.innerHTML = null;
                  notifi.removeAttribute("class");
                  notifi.appendChild(loader);
                },
                success: function(output){
                  if(output.status){
                    $(modal).modal("hide");
                    form_inisial.value = output.email;
                    form_inisial.setAttribute("readonly", "true");
                    check = true;
                    like_dislike(fungsi, id);
                  } else {
                    input.removeAttribute("disabled");
                    btn_submit.removeAttribute("disabled");
                    notifi.innerHTML = null;
                    notifi.setAttribute("class", "alert alert-danger");
                    notifi.textContent = output.message;
                  }
                }
              });
            });

            modal_footer.textContent = "SanctuaryCMS V.0.1";

          }



        }


        function refresh_komentar(data = false){
          if(data != false){

            var ik = document.getElementById("interface-kode");
            ik.textContent = data.kode;

            var i_komen = document.getElementById("interface-komentar");
            i_komen.innerHTML = null;


            for(var i =0; i < data.data.length; i++){
              var hr_k = document.createElement("hr");

              i_komen.appendChild(hr_k);

              var media = document.createElement("div");
              media.setAttribute("class", "media");
              i_komen.appendChild(media);

              var img_k = document.createElement("img");
              img_k.setAttribute("class", "mr-3");
              img_k.setAttribute("style", "width:100px;height:100px;");
              img_k.setAttribute("src", "//<?php echo base_url("assets/images/user.png"); ?>");
              media.appendChild(img_k);

              var media_body = document.createElement("div");
              media_body.setAttribute("class", "media-body");
              media.appendChild(media_body);

              var p_k = document.createElement("p");
              media_body.appendChild(p_k);

              var b_k = document.createElement("b");
              b_k.textContent = "Dari : ";
              p_k.appendChild(b_k);
              var i_k = document.createElement("i");
              i_k.textContent = data.data[i]["inisial"];
              b_k.appendChild(i_k);

              var hr_u = document.createElement("hr");
              media_body.appendChild(hr_u);

              var p_i = document.createElement("div");
              p_i.setAttribute("class", "jumbotron");
              p_i.setAttribute("style", "padding:5px;min-height:150px;");
              p_i.textContent = data.data[i]["komentar"];
              media_body.appendChild(p_i);

              var hr_i = document.createElement("hr");
              media_body.appendChild(hr_i);


              var div_tgl = document.createElement("div");
              div_tgl.innerHTML = "<i class='fa fa-calendar'></i> "+data.data[i]["tgl_kirim"]+" <i class='fa fa-clock-o'></i> "+data.data[i]["jam"];
              media_body.appendChild(div_tgl)
              
            }
          } else {
            alert("OK");
          }
        }
      </script>

      <hr>
      <h3 class="display-4" style="font-size: 20px;"><b><i>Komentar...</i></b> </h3>
      
      <div id="interface-komentar">
        <?php 
          if($komentar->status == true){
            foreach ($komentar->data as $key => $value) {
              ?>
                <hr>
                <div class="media">
                  <img src="//<?php echo base_url("assets/images/user.png"); ?>" class="mr-3" alt="Komentar" style="width: 100px;height: 100px;">
                  <div class="media-body">
                    <p><b>Dari : <i><?=$value["inisial"]?></i></b></p>
                    <hr>
                    <div class="jumbotron" style="padding: 10px;min-height: 150px;"><?=$value["komentar"]?></div>
                    <hr>
                    <div>
                      <i class="fa fa-calendar"></i> <?=$value["tgl_kirim"] ?> <i class="fa fa-clock-o"></i> <?=$value["jam"] ?>
                    </div>
                  </div>
                </div>
                
              <?php
            }
          } else {
            ?>
            <div class="alert alert-warning" align="center"><?=$komentar->message?></div>
            <?php
          }
         ?>
      </div>

      <br>

      <hr>
      <h3 class="display-4" style="font-size: 20px;"><b><i>Artikel yang berkaitan.....</i></b></h3>
      <hr>
    <div class="container">
    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="row">
            <div class="col">
              <div class="media">
              <img src="..." class="mr-3" alt="...">
              <div class="media-body">
                <h5 class="mt-0">Media heading</h5>
                <div class="row">
                  <div class="col" style="padding-right: 0;">
                    <small><i class="fa fa-eye"></i> 15</small> <small><i class="fa fa-thumbs-up"></i> 15</small> <small><i class="fa fa-thumbs-down"></i> 0</small> <small><i class="fa fa-comments"></i> 15</small> 
                  </div>
                  <div class="col" style="padding-left:2px;">
                    <small><i class="fa fa-calendar"></i> 13/04/2000</small> <small><i class="fa fa-pencil"></i> Admin</small>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <div class="col">
              <div class="media">
              <img src="..." class="mr-3" alt="...">
              <div class="media-body">
                <h5 class="mt-0">Media heading</h5>
                <div class="row">
                  <div class="col" style="padding-right: 0;">
                    <small><i class="fa fa-eye"></i> 15</small> <small><i class="fa fa-thumbs-up"></i> 15</small> <small><i class="fa fa-thumbs-down"></i> 0</small> <small><i class="fa fa-comments"></i> 15</small> 
                  </div>
                  <div class="col" style="padding-left:2px;">
                    <small><i class="fa fa-calendar"></i> 13/04/2000</small> <small><i class="fa fa-pencil"></i> Admin</small>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <div class="col">
              <div class="media">
              <img src="..." class="mr-3" alt="...">
              <div class="media-body">
                <h5 class="mt-0">Media heading</h5>
                <div class="row">
                  <div class="col" style="padding-right: 0;">
                    <small><i class="fa fa-eye"></i> 15</small> <small><i class="fa fa-thumbs-up"></i> 15</small> <small><i class="fa fa-thumbs-down"></i> 0</small> <small><i class="fa fa-comments"></i> 15</small> 
                  </div>
                  <div class="col" style="padding-left:2px;">
                    <small><i class="fa fa-calendar"></i> 13/04/2000</small> <small><i class="fa fa-pencil"></i> Admin</small>
                  </div>
                </div>
              </div>
            </div>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <div class="row">
            <div class="col">
              <div class="media">
              <img src="..." class="mr-3" alt="...">
              <div class="media-body">
                <h5 class="mt-0">Media heading</h5>
                <div class="row">
                  <div class="col" style="padding-right: 0;">
                    <small><i class="fa fa-eye"></i> 15</small> <small><i class="fa fa-thumbs-up"></i> 15</small> <small><i class="fa fa-thumbs-down"></i> 0</small> <small><i class="fa fa-comments"></i> 15</small> 
                  </div>
                  <div class="col" style="padding-left:2px;">
                    <small><i class="fa fa-calendar"></i> 13/04/2000</small> <small><i class="fa fa-pencil"></i> Admin</small>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <div class="col">
              <div class="media">
              <img src="..." class="mr-3" alt="...">
              <div class="media-body">
                <h5 class="mt-0">Media heading</h5>
                <div class="row">
                  <div class="col" style="padding-right: 0;">
                    <small><i class="fa fa-eye"></i> 15</small> <small><i class="fa fa-thumbs-up"></i> 15</small> <small><i class="fa fa-thumbs-down"></i> 0</small> <small><i class="fa fa-comments"></i> 15</small> 
                  </div>
                  <div class="col" style="padding-left:2px;">
                    <small><i class="fa fa-calendar"></i> 13/04/2000</small> <small><i class="fa fa-pencil"></i> Admin</small>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <div class="col">
              <div class="media">
              <img src="..." class="mr-3" alt="...">
              <div class="media-body">
                <h5 class="mt-0">Media heading</h5>
                <div class="row">
                  <div class="col" style="padding-right: 0;">
                    <small><i class="fa fa-eye"></i> 15</small> <small><i class="fa fa-thumbs-up"></i> 15</small> <small><i class="fa fa-thumbs-down"></i> 0</small> <small><i class="fa fa-comments"></i> 15</small> 
                  </div>
                  <div class="col" style="padding-left:2px;">
                    <small><i class="fa fa-calendar"></i> 13/04/2000</small> <small><i class="fa fa-pencil"></i> Admin</small>
                  </div>
                </div>
              </div>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    </div>
    <div class="col">
      <div class="jumbotron">
        ads
      </div>

      <div class="jumbotron">
        props
      </div>

    </div>
</div>

<hr>