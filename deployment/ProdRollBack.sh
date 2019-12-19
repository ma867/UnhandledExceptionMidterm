#!/bin/bash

scp -r /home/melissa/deployment melissa@192.168.2.11:/home/melissa/previousVersion/* 
echo "Production Rollback Successful"
sleep 5




