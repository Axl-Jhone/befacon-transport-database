$(document).ready( function () {
    $('#trips').DataTable({
        "ordering": false,
    });
} );

const open = document.getElementById('open')
const modal_wrapper = document.getElementById('modal_wrapper')
const close= document.getElementById('close')

open.addEventListener('click', () => {modal_wrapper.classList.add('show');});
close.addEventListener('click', () => {modal_wrapper.classList.remove('show');});