import "./bootstrap";

const swal = window.Swal;

document.querySelectorAll("[data-swal-success]").forEach((element) => {
    swal?.fire({
        icon: "success",
        title: "Berhasil",
        text: element.dataset.swalSuccess,
        timer: 2600,
        showConfirmButton: false,
    });
});

document.querySelectorAll("[data-swal-error]").forEach((element) => {
    swal?.fire({
        icon: "error",
        title: "Perlu diperiksa",
        text: element.dataset.swalError,
        confirmButtonText: "Mengerti",
    });
});

document.querySelectorAll("form[data-confirm]").forEach((form) => {
    form.addEventListener("submit", (event) => {
        if (form.dataset.confirmed === "true") return;
        event.preventDefault();
        swal?.fire({
            icon: "warning",
            title: "Konfirmasi tindakan",
            text: form.dataset.confirm,
            showCancelButton: true,
            confirmButtonText: "Ya, lanjutkan",
            cancelButtonText: "Batal",
            confirmButtonColor: "#456ff0",
        }).then((result) => {
            if (result.isConfirmed) {
                form.dataset.confirmed = "true";
                form.requestSubmit();
            }
        });
    });
});
