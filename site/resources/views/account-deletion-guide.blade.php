@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Account Delete Guide') }} </div>

                <div class="card-body">
                      Here are the steps for account deletion process                
                     <br>
                     <br>
                    
                     1. After logging in, click on Profile tab which is located at the bottom right corner of the screen.
                     <br>
                     2. This will take you to your User Profile screen. In your user profile, there is Settings icon represented as a gear or a similar icon. 
                     <br>
                     3. Click on Settings icon which will open the Settings screen.
                     <br>
                     4. On Settings screen menu, there is Account Settings option. Click on the option which will open account settings screen.
                     <br>
                     5. Then click on Delete Account option and follow given instructions to confirm deletion.
                     <br>
                     6. Once account deletion is success, all your data including personal information will be completely removed from our system
                     <br> 
                     <br>
                     <br>           
                    <b>Note :</b>  Please be careful, this process cannot be undone.



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
