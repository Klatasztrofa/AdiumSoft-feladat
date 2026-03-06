#!/bin/sh

echo 'npm ci futtatása'
npm ci
npm run dev -- --host
