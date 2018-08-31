<?php

namespace App;

class Member extends User
{
    public function services()
    {
        return $this->hasMany(Service::class, 'members_id', 'id');
    }

    public function requests()
    {
        return $this->hasMany(MemberRequest::class, 'members_id', 'id');
    }
    
    public function latestRequest()
    {
        return $this->hasOne(MemberRequest::class, 'members_id', 'id')
            ->orderBy('created_at', 'DESC');
    }

    public function scopeFindById($query, $id)
    {
        $query->where('id', $id)
            ->where('login_type', 1)
            ->where('status', 1);
    }
}
