# Sucker

web应用开发小作业，舔狗日记,后端一小时前端一整天。前端真的太难了。

### 作业完成度
* [x] 接口
    * [x] 查询
    * [x] 添加
    * [x] 更新
    * [ ] 删除 （觉得用不到）
* [x] 前端滑动更新
* [x] 简陋的加载遮罩
* [ ] 本地更新展示like\dislike（没解决,Vue没有检测到`obj in array`的变化)

### CHANGE LOG

#### 04.16 16:12

- 一天前端 测试时添加了跨源头部
- 拿之前爬的东西给card添加随机的背景 添加handler `RandomBg`

#### 04.15 22:09

- 还是同一个问题，不想配置pathinfo 放弃`/controller/method`
- 开始用传统的`api.php?c=controller&m=method`


##### 04.15 21:53

- 试图写成Gin的样子 
    - `[GET] /sucker/posts` ---> `function Show`
    - `[POST] /sucker/post` ---> `function Post`
- 突然意识到php无法自己serve，必须要借助web服务，那么就需要手动配置pathinfo 否则第一个总是脚本名字
- 遂放弃 考虑写成标准mvc 使用call_user_func

### ScreenShot

![sucker](http://q8ptr9gz2.bkt.clouddn.com/sucker.gif)
