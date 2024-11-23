from django.db import models

class Profile(models.Model):
    id_profile = models.AutoField(primary_key=True)
    first_name = models.CharField(max_length=50)
    last_name = models.CharField(max_length=50)
    username = models.CharField(max_length=100, unique=True)
    email_address = models.EmailField(unique=True)
    birthdate = models.DateField()
    password = models.CharField(max_length=128)
    
    class Meta:
        db_table = 'profile'

    def __str__(self):
        return self.username
