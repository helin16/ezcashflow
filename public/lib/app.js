dpd.users.login({"username": "admin", "password": "admin"}, function(user, err) {
  if(err) return console.log(err);
  console.log(user);
});