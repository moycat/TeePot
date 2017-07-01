{*********************************************************
 * /Web/view/common/userform.tpl @ TeePot
 *
 * Copyright (C) 2016 Moycat <moycat@makedie.net>
 *
 * This file is part of TeePot.
 *
 * TeePot is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TeePot is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TeePot. If not, see <http://www.gnu.org/licenses/>.
**********************************************************}

<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="loginLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="loginLabel">登录账户 (ﾉ>ω<)ﾉ</h4>
            </div>
            <div class="modal-body">
                <form id="login-form" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="username" class="col-sm-2 control-label"><b>用户名</b></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="username" placeholder="admin">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-sm-2 control-label"><b>密码</b></label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password" placeholder="admin888">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-2 col-sm-10">
                            <label>
                                <input name="remember" id="remember" type="checkbox"> 记住我
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary">登 ♂ 录</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="register-modal" tabindex="-1" role="dialog" aria-labelledby="registerLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="registerLabel">注册账户ヽ(๑•̀ω•́)ノ</h4>
            </div>
            <div class="modal-body">
                <div id="register-progress" class="progress" style="display: none;">
                    <div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">Processing...</span>
                    </div>
                </div>
                <form id="register-form" class="form-horizontal" role="form" autocomplete="off">
                    <div class="form-group">
                        <label for="newusername" class="col-sm-2 control-label"><b>用户名</b></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-input" name="username" id="newusername" pattern="[^\s]{ldelim}3,10{rdelim}" autocomplete="off">
                            <label for="newusername" class="floating-label">登录使用的用户名，3-10字符</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="newpassword" class="col-sm-2 control-label"><b>密码</b></label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control form-input" name="password" id="newpassword" pattern="[\x00-\x7F]{ldelim}6,50{rdelim}" autocomplete="off">
                            <label for="newpassword" class="floating-label">长度6-50位，限可显示的ASCII字符</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation" class="col-sm-2 control-label"><b>重复密码</b></label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control form-input" name="password_confirmation" id="password_confirmation" pattern="[\x00-\x7F]{ldelim}6,50{rdelim}" autocomplete="off">
                            <label for="password_confirmation" class="floating-label">再来一遍啦</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label"><b>电子邮箱</b></label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control form-input" name="email" id="email" autocomplete="off">
                            <label for="email" class="floating-label">请输入有效的电子邮箱</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-warning" onclick="register()">注 ♂ 册</button>
            </div>
        </div>
    </div>
</div>