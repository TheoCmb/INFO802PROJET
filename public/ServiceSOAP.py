# Utiliser Python 3.10.11

from spyne import Application, rpc, ServiceBase, Integer, Unicode, Iterable
from spyne.protocol.soap import Soap11
from spyne.server.wsgi import WsgiApplication

class HelloWorldService(ServiceBase):
    @rpc(Integer, Integer, Integer, _returns=Integer)
    def calcule_temps_trajet(ctx, distance, autonomie, temps_charge):
        res = 0
        if autonomie > distance+(distance*0.02) :
            res = 0 # Renvoie valeur indiquée par l'API google route
        else:
            res = 0 # Calcul bien compliqué
        return res

application = Application([HelloWorldService],
    tns='spyne.examples.hello',
    in_protocol=Soap11(validator='lxml'),
    out_protocol=Soap11()
)

if __name__ == '__main__':
    # You can use any Wsgi server. Here, we chose
    # Python's built-in wsgi server but you're not
    # supposed to use it in production.
    from wsgiref.simple_server import make_server

    wsgi_app = WsgiApplication(application)
    server = make_server('127.0.0.1', 8000, wsgi_app)
    server.serve_forever()