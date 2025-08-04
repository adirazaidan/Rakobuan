document.addEventListener("DOMContentLoaded",function(){const m=document.getElementById("menu-toggle"),c=document.querySelector(".sidebar"),l=document.createElement("div");l.className="sidebar-overlay",document.body.appendChild(l);const y=()=>{c.classList.add("open"),l.classList.add("active"),document.body.style.overflow="hidden"},f=()=>{c.classList.remove("open"),l.classList.remove("active"),document.body.style.overflow=""};m&&c&&m.addEventListener("click",function(t){t.stopPropagation(),c.classList.contains("open")?f():y()}),l.addEventListener("click",f);const i=document.getElementById("customAlertModal"),v=document.getElementById("alertModalTitle"),b=document.getElementById("alertModalMessage"),k=document.getElementById("alertModalIcon"),g=document.getElementById("alertModalConfirmBtn"),p=document.getElementById("closeAlertModalBtn");window.customAlert=function(t,n="info",e="Pesan"){if(!i)return;v.textContent=e,b.textContent=t;const a={success:"fas fa-check-circle",warning:"fas fa-exclamation-triangle",danger:"fas fa-times-circle",info:"fas fa-info-circle"};k.className=a[n]||a.info,i.className="modal-overlay",i.classList.add(`alert-${n}`),i.style.display="flex"};function d(){i&&(i.style.display="none",i.classList.remove("alert-success","alert-warning","alert-danger","alert-info"))}g&&g.addEventListener("click",d),p&&p.addEventListener("click",d),i&&i.addEventListener("click",t=>{t.target===i&&d()}),window.alert=function(t){customAlert(t,"info","Pesan")},window.confirm=function(t){return new Promise(n=>{const e=document.createElement("div");e.className="modal-overlay",e.style.display="flex",e.innerHTML=`
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Konfirmasi</h4>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="alert-icon-wrapper">
                            <i class="fas fa-question-circle" style="color: var(--warning-color);"></i>
                        </div>
                        <p class="alert-message">${t}</p>
                        <div class="alert-actions">
                            <button type="button" class="btn btn-secondary">Batal</button>
                            <button type="button" class="btn btn-primary">OK</button>
                        </div>
                    </div>
                </div>
            `,document.body.appendChild(e);const a=e.querySelector(".modal-close"),s=e.querySelector(".btn-secondary"),r=e.querySelector(".btn-primary"),o=u=>{e.remove(),n(u)};a.addEventListener("click",()=>o(!1)),s.addEventListener("click",()=>o(!1)),r.addEventListener("click",()=>o(!0)),e.addEventListener("click",u=>{u.target===e&&o(!1)})})},document.addEventListener("click",function(t){if(t.target.closest('button[type="submit"]')&&t.target.closest("form")){const n=t.target.closest('button[type="submit"]'),e=t.target.closest("form");if(e.querySelector('input[name="_method"][value="DELETE"]')||e.action.includes("destroy")||n.classList.contains("custom-action-btn-danger")){t.preventDefault();let a="Yakin ingin menghapus data ini?";e.action.includes("products")?a="Yakin ingin menghapus menu ini?":e.action.includes("outlets")?a="Apakah Anda yakin ingin menghapus outlet ini?":e.action.includes("orders")?a="Yakin ingin menghapus pesanan ini secara permanen?":e.action.includes("dining-tables")?a="Yakin ingin menghapus meja ini?":e.action.includes("discounts")?a="Yakin ingin menghapus diskon ini?":e.action.includes("categories")?a="Yakin ingin menghapus kategori ini? Semua menu di dalamnya juga akan terhapus.":e.action.includes("calls")&&(a="Yakin ingin menghapus panggilan ini?"),confirm(a).then(s=>{s&&e.submit()})}e.action.includes("updateStatus")&&e.querySelector('input[name="status"][value="cancelled"]')&&(t.preventDefault(),confirm("Yakin ingin membatalkan pesanan ini?").then(a=>{a&&e.submit()})),e.action.includes("updateStatus")&&(n.textContent.trim().includes("Batal")||n.querySelector("span")&&n.querySelector("span").textContent.trim()==="Batal")&&(t.preventDefault(),confirm("Yakin ingin membatalkan pesanan ini?").then(a=>{a&&e.submit()}))}}),document.addEventListener("click",function(t){if(t.target.classList.contains("clear-session-btn")){t.preventDefault();const n=t.target.closest("form");n&&confirm("Yakin ingin membersihkan sesi ini?").then(e=>{e&&n.submit()})}}),window.previewImage=function(t){const n=new FileReader,e=document.getElementById("img-preview");n.onload=function(){n.readyState==2&&(e.style.display="block",e.src=n.result)},n.readAsDataURL(t.target.files[0])},$(document).ready(function(){$(".select2").length&&$(".select2").select2({placeholder:"-- Pilih Menu --",allowClear:!0})}),window.showLogoutConfirm=function(){const t=document.createElement("div");t.className="modal-overlay",t.style.display="flex",t.innerHTML=`
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Konfirmasi Keluar</h4>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert-icon-wrapper">
                        <i class="fas fa-sign-out-alt" style="color: var(--warning-color);"></i>
                    </div>
                    <p class="alert-message">Apakah Anda yakin ingin keluar dari sistem admin?</p>
                    <div class="alert-actions">
                        <button type="button" class="btn btn-secondary">Batal</button>
                        <button type="button" class="btn btn-primary">Keluar</button>
                    </div>
                </div>
            </div>
        `,document.body.appendChild(t);const n=t.querySelector(".modal-close"),e=t.querySelector(".btn-secondary"),a=t.querySelector(".btn-primary"),s=()=>{t.remove()},r=()=>{s(),document.getElementById("logout-form").submit()};n.addEventListener("click",s),e.addEventListener("click",s),a.addEventListener("click",r),t.addEventListener("click",o=>{o.target===t&&s()})}});
