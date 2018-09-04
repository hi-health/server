# API使用方法(開發版本) #

**API Host: http://hi-health.myu6.com**

## 已完成的API項目 ##

### 取得台灣各縣市鄉鎮 ###

**GET** /api/parameters/cities

---

### 使用者登入 ###

**POST** /api/users/login

```
#!json
{
    "login_type": ["required", "in:1,2"],
    "account": ["required_without:facebook_id", "string"],
    "password": ["required_without:facebook_id", "string"],
    "facebook_id": ["required_without:account", "string"],
}
```

---

### 發送使用者簡訊驗證碼 ###

**POST** /api/users/sms/code

```
#!json
{
    "phone": ["required"]
}
```

---

### 使用者註冊 ###

**POST** /api/users/register

```
#!json
{
    "account": ["required", "string"],
    "password": ["required", "string", "min:6"],
    "login_type": ["required", "in:1,2"],
    "name": ["required", "string"],
    "male": ["required", "in:0,1"],
    "birthday": ["required", "date_format:Y-m-d"],
    "city_id": ["required"],
    "district_id": ["required"],
    //"avatar": ["image", "max:1024"],
    "facebook_id": [],
    "facebook_token": [],
}
```

---

### 新增使用者的Device Token至AWS SNS ###

**POST** /api/users/{user_id}/device_token

```
#!json
{
    "arn": ["required", "in:member-gcm,member-apn,doctor-gcm,doctor-apn"],
    "device_token": ["required", "string"],
}
```

---

### 修改使用者資料 ###

**POST** /api/users/{user_id}

**PUT** /api/users/{user_id}

```
#!json
{
    "name": ["required", "string"],
    "birthday": ["required", "date_format:Y-m-d"],
    "city_id": ["required"],
    "district_id": ["required"],
    "avatar": ["image", "max:1024"],
}
```

---

### 設定使用者為上線 ###

**POST** /api/users/{user_id}/online

---

### 設定使用者為下線 ###

**POST** /api/users/{user_id}/offline

---

### 取得會員資料 ###

**GET** /api/members/{user_id}

---

### 取得會員首頁資訊 ###

**GET** /api/members/{user_id}/summary

---

### 取得會員已發出的諮詢記錄 ###

**GET** /api/members/{member_id}/requests

**GET** /api/requests/members/{member_id}

兩項API皆相同

---

### 會員新增諮詢 ###

**POST** /api/requests/members/{member_id}

```
#!json
{
    "treatment_type": ["between:1,2"],
    "treatment_kind": ["between:1,4"],
    "onset_date": ["date_format:Y-m-d"],
    "onset_part": ["between:1,5"],
    "city_id": ["required"],
    "district_id": ["required"]
}
```

---

### 取得醫生資料 ###

**GET** /api/doctors/{doctor_id}

---

### 取得醫生資料 by number ###

**GET** /api/doctors/number/{doctor_number}

---

### 取得醫生首頁資訊 ###

**GET** /api/doctors/{doctor_id}/summary

---

### 醫師取得所有已付費過的客戶 ###

**GET** /api/doctors/{doctor_id}/services/members

---

### 查詢指定醫師與諮詢對話中的會員列表 ###

**GET** /api/doctors/{doctor_id}/requests/members

```
#!json
{
    "page": 1, // Default
    "per_page": 20 // Default
}
```

---

### 依經緯度查詢醫生 ###

**POST** /api/doctors/nearby

```
#!json
{
    "members_id":  ["required", "exists:users,id"],
    "longitude": ["required", "regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/"],
    "latitude": ["required", "regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/"],
    "distance": ["numeric", "min:1"]
}
```

---

### 依關鍵字查詢醫生 ###

**POST** /api/doctors/search

```
#!json
{
    "members_id":  ["required", "exists:users,id"],
    "keyword": ["required"],
    "city_id": ["parameters:key"],
    "treatment_type": ["in:1,2"]
}
```

---

### 依編號查詢服務明細 ###

**GET** /api/services/{service_id}

---

### 依編號查詢對該服務上傳過的影片名稱 ###

**GET** /api/services/{service_id}/videos

---
### 取得服務的電子發票 ###

**GET** /api/services/{service_id}/invoice

---

### 查詢醫生的服務統計 ###

**GET** /api/services/histories/doctors/{doctor_id}

```
#!json
{
    "per_page": 20
}
```

---

### 查詢會員的服務統計 ###

**GET** /api/services/histories/members/{member_id}

```
#!json
{
    "per_page": 20
}
```

---

### 匯出服務(發信單筆記錄) ###

**POST** /api/services/{service_id}/export

```
#!json
{
    "email": ["required", "email"]
}
```

---

### 匯出指定醫生當月的已完成服務 ###

**POST** /api/services/export/doctors/{doctor_id}

```
#!json
{
    "email": ["required", "email"]
}
```

---

### 建立新的服務 ###

**POST** /api/services

```
#!json
{
    "doctors_id": ["required", "exists:doctors,users_id"],
    "charge_amount": ["required"]
}
```

---

### 更新服務的付款方式 ###

**POST** /api/services/{service_id}/payment

```
#!json
{
    "members_id": ["required", "exists:users,id"],
    "payment_method": ["required", "in:1,2"]
}
```

---

### 更新服務的復健類型 ###

**POST** /api/services/{service_id}/treatment

```
#!json
{
    "treatment_type": ["required", "in:1,2"]
}
```
---

### 設定服務開始時間 ###

**POST** /api/services/{service_id}/start

---

### 設定服務結束時間 ###

**POST** /api/services/{service_id}/stop

---

### 取得付款頁面 ###

**GET** /services/{service_id}/purchase

---

### 金流返回網址 ###

**POST** /services/{service_id}/return

```
#!json
{
    // 金流系統回傳值
}
```

---

### 付款成功頁面 ###

**GET** /services/{service_id}/success

---

### 付款失敗頁面 ###

**GET** /services/{service_id}/failure

---

### 查詢課程及影片 ###

**GET** /api/services/{service_id}/plans

---

### 寫入課程及影片 ###

**POST** /api/services/{service_id}/plans

```
#!json
// *代表陣列
{
    "plans": ["required", "array"],
    "plans.*.started_at": ["required", "date_format:H:i"],
    "plans.*.stopped_at": ["required", "date_format:H:i"],
    "plans.*.weight": ["nullable", "integer"],
    "plans.*.videos": ["required", "array", "min:1", "max:5"],
    "plans.*.videos.*.file": ["mimetypes:video/avi,video/mpeg,video/mp4", "max: ".(30 * 1024)], // 限制影片單檔容量為30MB
    "plans.*.videos.*.weight": ["nullable", "integer"],
    "plans.*.videos.*.description": ["nullable", "string"],
}
```

---

### 更新課程及影片 ###

**PUT** /api/services/{service_id}/plans

**POST** /api/services/{service_id}/plans/update

```
#!json
// *代表陣列
{
    "plans": ["required", "array"],
    "plans.*.id": ["nullable", "integer"],
    "plans.*.started_at": ["required", "date_format:H:i"],
    "plans.*.stopped_at": ["required", "date_format:H:i"],
    "plans.*.weight": ["nullable", "integer"],
    "plans.*.videos": ["required", "array", "min:1", "max:5"],
    "plans.*.videos.*.id": ["nullable", "integer"],
    "plans.*.videos.*.file": ["mimetypes:video/avi,video/mpeg,video/mp4", "max: ".(30 * 1024)], // 限制影片單檔容量為30MB
    "plans.*.videos.*.weight": ["nullable", "integer"],
    "plans.*.videos.*.description": ["requried", "string"],
}
```

---

### 醫生端啟動/關閉影片template的錄製 ###

**POST** /api/services/{service_id}/plans/{plan_id}/videos/{video_id}/activate_record

```
#!json
// *代表陣列
{
    "activation_flag": ["between:-1,1"],
}
```
---

### 查詢template的錄製狀態 ###

**GET** /api/services/{service_id}/plans/{plan_id}/videos/{video_id}/activation_flag

---

### 新增或更新影片對應的template ###

***POST*** /api/services/{service_id}/plans/{plan_id}/videos/{video_id}/template

```
#!json
// *代表陣列
{
    "movement_template_data": ["required","array"],  // 5 * 取樣次數 * 9軸
}
```

---

### 刪除課程 ###

**DELETE** /api/services/{service_id}/plans

**POST** /api/services/{service_id}/plans/delete

```
#!json
// *代表陣列
{
    "plans": ["required", "array", "min:1"],
    "plans.*.id": ["required"]
}
```

---

### 刪除影片 ###

**DELETE** /api/services/{service_id}/plans/{plan_id}/videos

**POST** /api/services/{service_id}/plans/{plan_id}/videos/delete

```
#!json
// *代表陣列
{
    "videos": ["required", "array", "min:1"],
    "videos.*.id": ["required"]
}
```

---

### 查詢每日評分 ###

***GET*** /api/services/{service_id}/plans/daily

---

### 查詢指定日期評分 ###

*** GET *** /api/services/{service_id}/plans/daily/{date}

---

### 新增test與指定日期評分 ###

*** POST *** /api/services/{service_id}/plans/{plan_id}/daily

```
#!json
// *代表陣列
{
        "date" => ["required", "date_format:Y-m-d"],
        "video" => ["required"],
        "video.id" => ["required","integer"],
        "video.test_data.start_at" => ["required","date_format:Y-m-d H:i:s"],
        "video.test_data.stop_at" => ["required","date_format:Y-m-d H:i:s"],
        "video.test_data.repeat_time" => ["required","integer"],
        "video.test_data.data" => ["required","array"],  // $repeat_time * 取樣次數 * 9軸
}
```

---

### 更新test與指定日期評分 ###

*** PUT *** /api/services/{service_id}/plans/{plan_id}/daily

*** POST *** /api/services/{service_id}/plans/{plan_id}/daily/update

```
#!json
// *代表陣列
{
        "date" => ["required", "date_format:Y-m-d"],
        "video" => ["required"],
        "video.id" => ["required","integer"],
        "video.test_data.start_at" => ["required","date_format:Y-m-d H:i:s"],
        "video.test_data.stop_at" => ["required","date_format:Y-m-d H:i:s"],
        "video.test_data.repeat_time" => ["required","integer"],
        "video.test_data.data" => ["required","array"],  // $repeat_time * 取樣次數 * 9軸
}
```

---

### 匯出課程評分(發信單筆記錄) ###

**POST** /api/services/{service_id}/plans/export

```
#!json
{
    "email": ["required", "email"]
}
```

---

### 取得指定醫師與客戶的筆記 ###

**GET** /api/notes/doctors/{doctor_id}/members/{member_id}/latest

---

### 儲存醫師與客戶的筆記 ###

**POST** /api/notes

```
#!json
{
    "doctors_id": ["required", "exists:doctors,users_id"],
    "members_id": ["required", "exists:users,id"],
    "note": ["required"]
}
```

---

### 取得指定醫師與客戶的對話記錄 ###

**GET** /api/messages/doctors/{doctor_id}/members/{member_id}

```
#!json
{
    "first_id": 1, // 給當前第一筆的id，取回該id以前的資料(選填)
    "latest_id": 1, // 給當前最後一筆的id，取回該id之後的資料(選填)
    "per_page": 20 // 預設20筆
}
```

---

### 發送訊息 ###

**POST** /api/messages

```
#!json
{
    "source": ["required", "in:doctor,member"],
    "doctors_id": ["required", "exists:doctors,users_id"],
    "members_id": ["required", "exists:users,id"],
    "member_request_id": ["nullable", "exists:member_requests,id"],
    "message": ["required"],
    "visible": ["in:0,1"],
}
```

---

### 查詢所有諮詢資料 ###

**GET** /api/requests

```
#!json
{
    "city_id": "{city_id}", // Option
    "page": 1, // Default
    "per_page": 20 // Default
}
```

---

### 查詢指定編號的諮詢資料 ###

**GET** /api/requests/{request_id}

---

## 安裝流程 ##

```
composer update
npm install
chmod 777 -Rf storage bootstrap/cache
cp .env.production .env
php artisan migrate
php artisan route:cache
php artisan config:cache
php artisan db:seed
```

## 測試案例 ##

```
#!shell

phpunit
```
