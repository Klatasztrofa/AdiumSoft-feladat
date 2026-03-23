import { useState } from "react";

export function Greeter() {
  const [name, setName] = useState("vendég");
  const [error, setError] = useState<string | null>(null);

  const onGreetingRequest = async () => {
    if (name.trim().length === 0) {
      setError("Kötelező kitölteni");
      return;
    }

    setError(null);

    try {
      const greeting = await getGreeting(name);
      window.alert(greeting);
    } catch (e) {
      window.alert(`hiba: ${e}`);
    }
  };

  return (
    <div className="py-4 px-8 max-w-md mx-auto mt-8 border-2 border-solid border-amber-500 rounded-lg">
      <div className="flex flex-col">
        <label htmlFor="name" className="p-2 font-bold">
          Név
        </label>
        <input
          id="name"
          defaultValue={name}
          onChange={(e) => {
            setName(e.target.value);
          }}
          className="p-2 border-b-1 bg-gray-100 rounded-t-md focus:border-b-transparent"
        />
        {error && <span className="text-red-500 text-sm">{error}</span>}
        <button
          onClick={(e) => {
            e.preventDefault();
            onGreetingRequest();
          }}
          className="mt-16 py-2 px-4 font-bold bg-amber-300 hover:bg-amber-400 active:bg-amber-500 rounded-lg cursor-pointer"
        >
          Üdvözlést kérek!
        </button>
      </div>
    </div>
  );
}

async function getGreeting(name: string): Promise<string> {
  const baseUrl = import.meta.env.VITE_API_URL;

  const res = await fetch(`${baseUrl}/hello?name=${name}`);
  if (!res.ok) {
    const err = await res.text();
    throw Error(err);
  }
  const body = (await res.json()) as { message: string };
  return body.message;
}
