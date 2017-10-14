<?php include "header.php"?>
    <div id="container" class="container">

        <h2 style="margin-bottom: 0">
            {{notice_info.notice_title}}
        </h2>
        <p class="grey-text text-lighten-1" style="margin-top:0">发送给: {{groups|array2String}}</p>
        <div class="divider"></div>
        <div>
            <pre>{{notice_info.notice_content}}</pre>
        </div>
        <div class="divider"></div>
        <h5>未回复:</h5>
        <p class="red-text">{{no_answer|array2String}}</p>
        <div class="divider"></div>
        <button class="btn-floating btn waves-effect waves-light teal right" @click="answer_notice"><i class="material-icons">add</i></button>

        <h5>已回复:</h5>
            <ul class="collection">
                <li class="collection-item avatar" v-for="answer in answers">
                    <img :src="answer.user_avatar" alt="" class="circle">
                    <span class="title teal-text">{{answer.user_nickname}}</span>
                    <p>
                        {{answer.answer_info}}
                    </p>

                </li>
            </ul>
        <div id="answer_modal" class="modal">
            <div class="modal-content" style="padding-bottom: 0">
                <form class="col s12">
                    <div class="row" style="margin-bottom: 0">
                        <div class="input-field col s12">
                            <textarea id="answer_area" class="materialize-textarea ">收到</textarea>
                            <label for="answer_area">回复内容</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class=" modal-action modal-close waves-effect waves-green btn-flat" @click="setNoticeRead">提交</button>
            </div>
        </div>
    </div>

    <script>
        var basic_url="<?php echo _HTTP;?>";
        var vm=new Vue(
            {
                el: "#container",
                data: {
                    notice_info:{},
                    groups:{},
                    answers:[],
                    no_answer:[],
                    notice_id:<?php echo $notice_id;?>
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
                    axios.get(basic_url + 'noticeAPI/getNoticeInfo/id/' + this.notice_id)
                        .then(function(response)
                        {
                            if(response.data.status==0){
                                vm.notice_info=response.data.notice_info;
                                vm.groups=response.data.groups;
                            }
                            else
                            {
                                Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                            }
                        });
                    axios.get(basic_url + 'noticeAPI/getNoticeAnswerStatus/id/' + this.notice_id)
                        .then(function(response)
                        {
                            if(response.data.status==0){
                                vm.answers=response.data.notice_answers;
                                vm.no_answer=response.data.unread;
                            }
                            else
                            {
                                Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                            }
                        });

                },
                methods: {
                    answer_notice:function()
                    {
                        $('#answer_modal').modal('open');
                    },
                    setNoticeRead:function()
                    {
                        var obj={
                            notice_id:vm.notice_id,
                            answer:$('#answer_area').val()
                        };

                        axios.post(basic_url + 'userAPI/replyNotice/',JSON.stringify(obj))
                            .then(function(response)
                            {
                                if(response.data.status==0) {
                                    Materialize.toast('<span class="">回复成功！正在跳转...</span>', 2000);
                                    delayJump(basic_url + "notice/read/id/" + vm.notice_id,1000);
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