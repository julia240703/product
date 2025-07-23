<div class="col-lg-3 mb-4">
    <div class="card border shadow-0 h-100">
        <div class="card-body text-center">
            <img src="{{ asset('storage/files/photo/' . $profile->photo)}}" alt="avatar" class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px;">
            <p class="text-bold card-title">{{ $profile->name }}</p>
            <p class="card-text mb-3">{{ $profile->email }}</p>

            @if ($loggedInUser->type === 'admin')
                <a href="{{ route('users.show', $profile->user_id) }}" type="button" class="btn btn-success">View Profile</a>
            @else
                <a href="{{ route('managerusers.show', $profile->user_id) }}" type="button" class="btn btn-success">View Profile</a>
            @endif


        </div>
    </div>
</div>
