<?php include "header.php"?>
    <div id="container" class="container">

        <div class="row" style="margin-bottom: 0;">
            <div class="col s12 l6 offset-l3">
                <div class="card">
                    <div class="card-content blue  white-text">
                        <span class="card-title">我发布的通知:</span>
                        <ul class="collapsible popover teal-text" data-collapsible="accordion">
                            <li v-for="notice in read_notices_info">
                                <div class="collapsible-header">{{ notice.notice_title }}</div>
                                <div class="collapsible-body white" style="padding-bottom: 0;">
                                    <p>{{notice.notice_content}}</p>
                                    <div class="row right-align">
                                        <a class="waves-effect waves-light btn red" :href="notice.notice_id | noticeView">详情</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var basic_url="<?php echo _HTTP;?>";
        var vm=new Vue(
            {
                el: "#container",
                data: {
                    groups_info:[],
                    notices_info:[],
                    read_notices_info:[],
                    cur_notice_info:{}
                },
                filters: {
                    noticeView:function(id)
                    {
                        return basic_url + "notice/read/id/"+id;
                    }
                },
                created: function(){
                    axios.get(basic_url + 'userAPI/getMyCreateNotice/')
                        .then(function(response)
                        {
                            if(response.data.status==0){
                                var notices=response.data.notices;
                                vm.read_notices_info=notices;
                                Vue.nextTick(function () {
                                    $('.collapsible').collapsible();
                                });
                            }
                            else
                            {
                                Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                            }
                        });
                },
                methods: {

                }
            }
        );
    </script>
<?php include "footer.php"?>