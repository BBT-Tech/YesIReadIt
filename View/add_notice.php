<?php include "header.php"?>
    <div id="container" class="container">
        <div class="row">
            <div class="input-field col s12">
                <input id="notice_title" type="text" class="validate" v-model.trim="notice_title">
                <label for="notice_title">通知标题</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
                <textarea id="notice_content" class="materialize-textarea" v-model.trim="notice_content"></textarea>
                <label for="notice_content">通知内容</label>
            </div>
        </div>
        <div class="row">
            <ul class="collection with-header">
                <li class="collection-header"><h5>通知组别</h5></li>
                <li class="collection-item" v-for="group in privilege_groups">
                    <input type="checkbox" :id="'group' + group.group_id" :value="group.group_id" name="notice_group" />
                    <label :for="'group' + group.group_id">{{group.group_name}}</label>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class=" col s12">
                <label>通知有效期限:</label>
                <input id="notice_end_time" type="datetime-local" class="validate no-margin" placeholder="">
            </div>
        </div>
        <div class="row">
            <button class="waves-effect waves-light btn right" @click="submitNotice">创建通知</button>
        </div>

    </div>
    <script>
        var basic_url="<?php echo _HTTP;?>";
        var vm=new Vue(
            {
                el: "#container",
                data: {
                    notice_title:"",
                    notice_content:"",
                    privilege_groups:[]

                },
                filters: {
                    array2String:function(arr)
                    {
                        var ret="";
                        for(var x in arr)
                        {
                            ret=ret+arr[x]+" ";
                        }
                        return ret;
                    }
                },
                created: function(){
                    axios.get(basic_url + 'userAPI/getPrivilegeGroups/')
                        .then(function(response)
                        {
                            if(response.data.status==0){
                                if(response.data.groups.length==0)
                                    Materialize.toast('<span class="">看起来你没有任何组可以发布信息哦</span>' , 2000);

                                vm.privilege_groups=response.data.groups;
                            }
                            else
                            {
                                Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                            }
                        });

                },
                methods: {
                    submitNotice:function()
                    {
                        var groups=[];
                        $('input[name="notice_group"]:checked').each(function(){
                            groups.splice(-1,0,$(this).val());
                        });

                        var obj={
                            notice_title:this.notice_title,
                            notice_content:this.notice_content,
                            notice_groups:groups,
                            notice_end_time:Date.parse($('#notice_end_time').val())/1000
                        };
                        axios.post(basic_url + 'noticeAPI/newNotice/',JSON.stringify(obj))
                            .then(function(response)
                            {
                                if(response.data.status==0){
                                    Materialize.toast('<span class="">创建成功！正在跳转</span>' , 2000);
                                    delayJump(basic_url + 'notice/read/id/'+response.data.notice_id,1000);

                                }
                                else
                                {
                                    Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                                }
                            });
                    }
                }
            }
        );
    </script>
<?php include "footer.php"?>