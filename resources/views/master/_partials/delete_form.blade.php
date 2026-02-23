<form action="{{ $action }}" method="POST" class="d-inline"
      onsubmit="return confirm('Yakin hapus data ini?')">
  @csrf
  @method('DELETE')
  <button class="btn btn-sm btn-outline-danger" type="submit">
    <i class="bi bi-trash"></i>
  </button>
</form>