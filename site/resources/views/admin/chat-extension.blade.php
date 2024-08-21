
    <!-- Your existing extension content goes here -->

    <div class="card box-default">
    <div class="card-header with-border">
        <h3 class="card-title">Chat Room</h3>

        <div class="card-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-bs-toggle="collapse" href="#environment" role="button" aria-expanded="true" aria-controls="environment">
                <i class="icon-minus"></i>
            </button>
        </div>
    </div>

    <!-- /.box-header -->
    <div class="card-body collapse show" id="environment">
        <div class="table-responsive">
            <table class="table table-striped">
                @foreach($chats as $chat)
                    <li class="item">
                        <div class="product-img">
                            <!-- <i class="icon- fa-2x ext-icon"></i> -->
                        </div>
                        <div class="product-info">
                            <a href="" target="_blank" class="product-title">
                                {{ $chat->message }}
                            </a>
                            
                        </div>
                    </li>
                @endforeach
                
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.box-body -->
</div>
