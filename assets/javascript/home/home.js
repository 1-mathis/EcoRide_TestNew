import '../../styles/home/home.css'

const btn = document.querySelector('.swap');

if (btn) {
btn.addEventListener('click', () => {
    const from = document.getElementById('from');
    const to = document.getElementById('to');
    [from.value, to.value] = [to.value, from.value];
    to.focus();
});
}