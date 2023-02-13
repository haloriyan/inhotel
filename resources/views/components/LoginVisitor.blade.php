<div class="modal" id="LoginVisitor">
    <div class="modal-body" style="width: 40%;">
        <form action="{{ route('visitor.login') }}" class="modal-content" onsubmit="loginVisitor(event)">
            {{ csrf_field() }}
            <h3 class="mt-0">Halo, boleh kami sedikit lebih mengenal tentangmu?</h3>
            <div class="group">
                <input type="text" name="name" id="name" required>
                <label for="name" class="active">Nama</label>
            </div>
            <div class="group">
                <input type="text" name="email" id="email">
                <label for="email">Email</label>
            </div>
            <div class="group">
                <input type="text" name="phone" id="phone">
                <label for="phone">No. Whatsapp</label>
            </div>

            <button class="primary w-100 mt-2">Kirim</button>
        </form>
    </div>
</div>