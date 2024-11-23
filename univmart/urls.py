from django.contrib import admin
from django.urls import path, include
from django.contrib.auth import views as auth_views
from . import views

urlpatterns = [
    path('admin/', admin.site.urls),
    path('', views.home, name='home'),
    path('signup/', views.signup, name='signup'),
    path('newsfeed/', views.newsfeed, name='newsfeed'),
    path('reset_password/', views.reset_password, name='reset_password'),
]

