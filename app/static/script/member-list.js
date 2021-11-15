axios.all([axios.get(`/members`)]).then(
  axios.spread((res1) => {
    var members = res1.data ? res1.data : [];
    members = members.filter((member) => member.role !== "ROLE_ADMIN");
    new Vue({
      el: "#members",
      data: {
        members: members,
      },
      methods: {
        redirect: function (id) {
          window.location.href = `/member-info/${id}`;
        },
      },
    });
  })
);
