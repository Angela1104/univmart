from django.db import migrations, models
import django.db.models.deletion

class Migration(migrations.Migration):

    dependencies = [

    ]

    operations = [
        migrations.CreateModel(
            name='Profile',
            fields=[
                ('id', models.BigAutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('first_name', models.CharField(max_length=50)),
                ('last_name', models.CharField(max_length=50)),
                ('username', models.CharField(max_length=100, unique=True)),
                ('email_address', models.EmailField(unique=True)),
                ('birthdate', models.DateField()),
                ('password', models.CharField(max_length=255)),
            ],
        ),
    ]
