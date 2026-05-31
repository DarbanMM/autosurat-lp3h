#!/bin/bash
# AutoSurat Database Switcher - Mac/Linux

echo ""
echo "===================================="
echo "  AutoSurat Database Switcher"
echo "===================================="
echo ""

if [ -z "$1" ]; then
    echo "Usage: ./switch-db.sh [option]"
    echo ""
    echo "Options:"
    echo "  local       Use local Docker PostgreSQL"
    echo "  supabase    Use Supabase PostgreSQL"
    echo ""
    exit 1
fi

if [ "$1" = "local" ]; then
    echo "Switching to LOCAL database..."
    cp .env.local .env
    cp docker-compose.local.yml docker-compose.yml
    echo ""
    echo "✓ Switched to LOCAL database!"
    echo ""
    echo "To start: docker-compose up -d"
    echo ""
elif [ "$1" = "supabase" ]; then
    echo "Switching to SUPABASE database..."
    cp .env.supabase .env
    cp docker-compose.supabase.yml docker-compose.yml
    echo ""
    echo "✓ Switched to SUPABASE database!"
    echo ""
    echo "To start: docker-compose up -d"
    echo ""
else
    echo "Error: Invalid option \"$1\""
    echo ""
    echo "Usage: ./switch-db.sh [local|supabase]"
    exit 1
fi
