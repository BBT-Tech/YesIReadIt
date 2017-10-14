<?php include "header.php"?>
    <div id="container" class="container">
        <div class="row">
            <div class="input-field col s12">
                <input id="user_nickname" type="text" class="validate" v-model.trim="user_nickname" value="-">
                <label for="user_nickname">个人姓名</label>
            </div>
        </div>
        <div class="row">
            <ul class="collection with-header">
                <li class="collection-header"><h5>所属组</h5></li>
                <li class="collection-item" v-for="group in groups">
                    <input type="checkbox" :id="'group' + group.group_id" :value="group.group_id" name="notice_group" v-model="check_groups[group.group_id]"/>
                    <label :for="'group' + group.group_id">{{group.group_name}}</label>
                </li>
            </ul>
            </div>
        <div class="row">
            <ul class="collection with-header">
                <li class="collection-header"><h5>有权限的组</h5></li>
                <li class="collection-item" v-for="group in privilege_groups">
                    <input type="checkbox" :id="'groupp' + group.group_id" :value="group.group_id" name="notice_groupp" checked disabled />
                    <label :for="'groupp' + group.group_id">{{group.group_name}}</label>
                </li>
            </ul>
            </div>
        <div class="row">
            <button class="waves-effect waves-light btn right" @click="saveInfo">保存</button>
        </div>

    </div>
    </div>
    <script>
        var basic_url="<?php echo _HTTP;?>";
        var vm=new Vue(
            {
                el: "#container",
                data: {
                    user_nickname:"-",
                    groups:[],
                    privilege_groups:[],
                    check_groups:[]

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
                    axios.get(basic_url + 'userAPI/getAllGroups/')
                        .then(function(response)
                        {
                            if(response.data.status==0){
                                for(var index in response.data.groups)
                                {
                                    var group=response.data.groups[index];
                                    vm.check_groups[group.group_id]=false;
                                }
                                vm.groups=response.data.groups;
                                axios.get(basic_url + 'userAPI/getUserInfo/')
                                    .then(function(response)
                                    {
                                        if(response.data.status==0){
                                            vm.user_nickname=response.data.user_info.user_nickname;
                                            var groups=response.data.groups;
                                            for(var index in groups)
                                            {
                                                vm.check_groups[groups[index].group_id]=true;
                                            }
                                        }
                                        else
                                        {
                                            Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                                        }
                                    });
                            }
                            else
                            {
                                Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                            }
                        });
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
                    saveInfo:function()
                    {

                        var groups=[];
                        for(var x in vm.check_groups)
                        {
                            if(vm.check_groups[x]===true)
                                groups.splice(-1,0,x);
                        }
                        var obj={
                            user_nickname:vm.user_nickname,
                            groups:groups
                        };

                        axios.post(basic_url + 'userAPI/saveUserInfo/',JSON.stringify(obj))
                            .then(function(response)
                            {
                                if(response.data.status==0){
                                    Materialize.toast('<span class="">保存成功！正在跳转</span>' , 2000);
                                    delayRefresh(500);
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